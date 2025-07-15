<?php

namespace App\Services;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

class VerificationService
{
    protected $jumioClient;
    protected $jumioApiToken;
    protected $jumioApiSecret;
    protected $jumioCallbackUrl;

    public function __construct()
    {
        $this->jumioApiToken = config('services.jumio.api_token');
        $this->jumioApiSecret = config('services.jumio.api_secret');
        $this->jumioCallbackUrl = config('services.jumio.callback_url');

        if ($this->jumioApiToken && $this->jumioApiSecret) {
            $this->jumioClient = new Client([
                'base_uri' => 'https://api.jumio.com/api/v1/',
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'SecureHousing/1.0',
                    'Authorization' => 'Basic ' . base64_encode($this->jumioApiToken . ':' . $this->jumioApiSecret),
                ],
            ]);
        }
    }

    public function initiateIdentityVerification(User $user, array $data, string $provider = 'manual'): Verification
    {
        $existingVerification = $user->verifications()
            ->where('verification_type', 'identity')
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        if ($existingVerification) {
            if ($existingVerification->status === 'pending') {
                throw new Exception('You already have a pending identity verification request.');
            }
            if ($existingVerification->status === 'verified') {
                throw new Exception('You are already identity verified.');
            }
        }

        $documents = [];
        $basePath = "verifications/identity/{$user->id}";

        foreach (['identity_document_front', 'identity_document_back', 'selfie'] as $docType) {
            if (isset($data[$docType]) && $data[$docType] instanceof UploadedFile && $data[$docType]->isValid()) {
                $path = $data[$docType]->store($basePath, 'private');
                $documents[$docType . '_path'] = $path;
            } elseif ($docType !== 'identity_document_back' && !isset($data[$docType])) {
                throw new Exception("Missing or invalid file for {$docType}.");
            }
        }

        $verificationData = [
            'country' => $data['country'] ?? null,
            'document_type' => $data['document_type'] ?? null,
            'identity_document_number' => $data['identity_document_number'] ?? null,
            'documents' => $documents,
            'submitted_at' => now()->toIso8601String(),
            'user_agent' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
        ];

        $externalId = null;
        $initialStatus = 'pending';

        if ($provider === 'jumio' || (config('services.jumio.api_token') && config('services.jumio.api_secret'))) {
            $jumioResult = $this->attemptJumioVerification($user, $documents);
            $initialStatus = $jumioResult['status'];
            $provider = $jumioResult['provider'];
            $externalId = $jumioResult['external_id'];
            $verificationData['jumio_data'] = $jumioResult['data'] ?? null;
        }

        $verification = $user->verifications()->create([
            'verification_type' => 'identity',
            'status' => $initialStatus,
            'provider' => $provider,
            'external_id' => $externalId,
            'verification_data' => $verificationData,
        ]);

        if ($initialStatus === 'verified') {
            $verification->update([
                'verified_at' => now(),
                'expires_at' => now()->addYears(2),
            ]);
            $verification->user->updateTrustScore();
        }

        return $verification;
    }

    public function initiateStudentVerification(User $user, array $data): Verification
    {
        if (!$user->isStudent()) {
            throw new Exception('Only student users can initiate student verification.');
        }

        $existingVerification = $user->verifications()
            ->where('verification_type', 'student')
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        if ($existingVerification) {
            if ($existingVerification->status === 'pending') {
                throw new Exception('You already have a pending student verification request.');
            }
            if ($existingVerification->status === 'verified') {
                throw new Exception('You are already student verified.');
            }
        }

        $documents = [];
        $basePath = "verifications/student/{$user->id}";

        foreach (['enrollment_document', 'student_id_card'] as $docType) {
            if (isset($data[$docType]) && $data[$docType] instanceof UploadedFile && $data[$docType]->isValid()) {
                $path = $data[$docType]->store($basePath, 'private');
                $documents[$docType . '_path'] = $path;
            } elseif ($docType === 'enrollment_document' && !isset($data[$docType])) {
                throw new Exception("Missing or invalid file for {$docType}.");
            }
        }

        $verificationData = [
            'university' => $data['university'] ?? null,
            'student_id' => $data['student_id'] ?? null,
            'enrollment_year' => $data['enrollment_year'] ?? null,
            'degree_program' => $data['degree_program'] ?? null,
            'documents' => $documents,
            'submitted_at' => now()->toIso8601String(),
            'user_agent' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
        ];

        $verification = $user->verifications()->create([
            'verification_type' => 'student',
            'status' => 'pending',
            'provider' => 'manual',
            'verification_data' => $verificationData,
        ]);

        return $verification;
    }

    private function attemptJumioVerification(User $user, array $documentPaths): array
    {
        if (!config('services.jumio.api_token') || !config('services.jumio.api_secret')) {
            return ['status' => 'pending', 'provider' => 'manual', 'external_id' => null, 'data' => null];
        }

        try {
            $idFrontContent = Storage::disk('private')->get($documentPaths['identity_document_front_path'] ?? null);
            $idBackContent = Storage::disk('private')->get($documentPaths['identity_document_back_path'] ?? null);
            $selfieContent = Storage::disk('private')->get($documentPaths['selfie_path'] ?? null);

            Log::info("Simulating sending verification for user {$user->id} to Jumio.");

            $simulatedResponse = [
                'scanReference' => 'jumio_' . Str::uuid(),
                'verificationStatus' => 'PENDING',
                'transactionId' => Str::uuid(),
            ];

            return [
                'status' => 'pending',
                'provider' => 'jumio',
                'external_id' => $simulatedResponse['scanReference'],
                'data' => $simulatedResponse,
            ];
        } catch (Exception $e) {
            Log::error('Jumio API error during attemptJumioVerification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return ['status' => 'pending', 'provider' => 'manual', 'external_id' => null, 'data' => null];
        }
    }

    public function handleJumioCallback(Request $request): bool
    {
        $callbackData = $request->all();
        Log::info("Jumio Callback Received: ", $callbackData);

        $externalId = $callbackData['scanReference'] ?? null;
        $verificationStatus = $callbackData['verificationStatus'] ?? 'ERROR';

        if ($externalId) {
            $verification = Verification::where('external_id', $externalId)->first();

            if ($verification) {
                $updateData = [
                    'verification_data' => array_merge(
                        $verification->verification_data,
                        ['jumio_callback' => $callbackData]
                    )
                ];

                switch ($verificationStatus) {
                    case 'APPROVED_VERIFIED':
                        $updateData['status'] = 'verified';
                        $updateData['verified_at'] = now();
                        $updateData['expires_at'] = now()->addYears(2);
                        break;
                    case 'DENIED_FRAUD':
                    case 'DENIED_UNSUPPORTED_ID_TYPE':
                    case 'DENIED_UNSUPPORTED_ID_COUNTRY':
                        $updateData['status'] = 'rejected';
                        $updateData['rejection_reason'] = $callbackData['rejectReason'] ?? 'Rejected by Jumio.';
                        break;
                    default:
                        break;
                }

                $verification->update($updateData);
                $verification->user->updateTrustScore();
                Log::info("Jumio verification {$verification->id} for user {$verification->user_id} updated to {$verification->status}.");
                return true;
            } else {
                Log::warning("Jumio callback received for unknown external ID: {$externalId}");
                return false;
            }
        }
        return false;
    }

    public function getDocumentUrl(Verification $verification, string $documentKey): ?string
    {
        $verificationData = $verification->verification_data;
        $documents = $verificationData['documents'] ?? [];
        if (!isset($documents[$documentKey])) {
            return null;
        }
        return Storage::disk('private')->url($documents[$documentKey]);
    }

    public function approveVerification(Verification $verification, User $admin, ?string $notes = null): bool
    {
        $verification->update([
            'status' => 'verified',
            'verified_at' => now(),
            'expires_at' => $verification->verification_type === 'identity' ? now()->addYears(2) : now()->addYear(),
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        $verification->user->updateTrustScore();
        Log::info("Verification {$verification->id} approved by admin {$admin->id}.");
        return true;
    }

    public function rejectVerification(Verification $verification, User $admin, string $reason, ?string $notes = null): bool
    {
        $verification->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        $verification->user->updateTrustScore();
        Log::info("Verification {$verification->id} rejected by admin {$admin->id}. Reason: {$reason}");
        return true;
    }
}
