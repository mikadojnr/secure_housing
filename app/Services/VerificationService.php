<?php

namespace App\Services;

use App\Models\Verification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VerificationService
{
    protected $jumioApiToken;
    protected $jumioApiSecret;
    protected $callbackUrl;

    public function __construct()
    {
        $this->jumioApiToken = config('services.jumio.api_token');
        $this->jumioApiSecret = config('services.jumio.api_secret');
        $this->callbackUrl = config('services.jumio.callback_url');
    }

    public function initiateIdentityVerification(User $user, array $data)
    {
        // Check if Jumio credentials are available
        if (!$this->jumioApiToken || !$this->jumioApiSecret) {
            // Manual verification: Store data and mark as pending for admin review
            $verification = Verification::create([
                'user_id' => $user->id,
                'verification_type' => 'identity',
                'status' => 'pending',
                'provider' => 'manual',
                'verification_data' => [
                    'country' => $data['country'],
                    'document_type' => $data['document_type'],
                    'identity_document_front_path' => $data['identity_document_front_path'],
                    'identity_document_back_path' => $data['identity_document_back_path'],
                    'identity_document_number' => $data['identity_document_number'],
                    'selfie_path' => $data['selfie_path'],
                    'home_town_address' => $data['home_town_address'],
                    'next_of_kin' => $data['next_of_kin'],
                ],
            ]);

            return [
                'success' => true,
                'message' => 'Identity verification submitted for manual review.',
                'verification_id' => $verification->id,
            ];
        }

        // Jumio integration
        try {
            $response = Http::withBasicAuth($this->jumioApiToken, $this->jumioApiSecret)
                ->post('https://api.jumio.com/api/v3/scans', [
                    'customerInternalReference' => $user->id,
                    'callbackUrl' => $this->callbackUrl,
                    'country' => $data['country'],
                    'type' => $data['document_type'],
                    'idNumber' => $data['identity_document_number'],
                    'frontsideImage' => base64_encode(Storage::disk('private')->get($data['identity_document_front_path'])),
                    'backsideImage' => $data['identity_document_back_path'] ? base64_encode(Storage::disk('private')->get($data['identity_document_back_path'])) : null,
                    'faceImage' => base64_encode(Storage::disk('private')->get($data['selfie_path'])),
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $verification = Verification::create([
                    'user_id' => $user->id,
                    'verification_type' => 'identity',
                    'status' => 'pending',
                    'provider' => 'jumio',
                    'external_id' => $result['scanReference'] ?? null,
                    'verification_data' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'Identity verification initiated with Jumio.',
                    'verification_id' => $verification->id,
                    'redirect_url' => $result['redirectUrl'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to initiate Jumio verification: ' . ($response->json()['error'] ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Jumio verification failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Failed to initiate Jumio verification: ' . $e->getMessage(),
            ];
        }
    }

    public function initiateStudentVerification(User $user, array $data)
    {
        // Manual verification: Store data and mark as pending for admin review
        $verification = Verification::create([
            'user_id' => $user->id,
            'verification_type' => 'student',
            'status' => 'pending',
            'provider' => 'manual',
            'verification_data' => [
                'university' => $data['university'],
                'student_id' => $data['student_id'],
                'document_path' => $data['document_path'],
            ],
        ]);

        return $verification;
    }

    public function handleCallback(array $payload)
    {
        // Handle Jumio callback
        $scanReference = $payload['scanReference'] ?? null;
        if (!$scanReference) {
            throw new \Exception('Invalid callback payload: Missing scanReference');
        }

        $verification = Verification::where('external_id', $scanReference)->firstOrFail();

        // Update verification status based on Jumio response
        $status = $payload['verificationStatus'] ?? 'PENDING';
        $mappedStatus = match ($status) {
            'APPROVED_VERIFIED' => 'verified',
            'DENIED_FRAUD', 'DENIED_REJECTED' => 'rejected',
            default => 'pending',
        };

        $verification->update([
            'status' => $mappedStatus,
            'rejection_reason' => $payload['rejectReason'] ?? null,
            'verified_at' => $mappedStatus === 'verified' ? now() : null,
            'expires_at' => $mappedStatus === 'verified' ? now()->addYear() : null,
            'verification_data' => array_merge($verification->verification_data, [
                'callback_data' => $payload,
            ]),
        ]);

        return $verification;
    }
}
