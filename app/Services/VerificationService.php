<?php

namespace App\Services;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerificationService
{
    protected $jumioApiUrl;
    protected $jumioApiToken;
    protected $jumioApiSecret;

    public function __construct()
    {
        $this->jumioApiUrl = config('services.jumio.api_url', 'https://api.jumio.com');
        $this->jumioApiToken = config('services.jumio.api_token', 'demo_token');
        $this->jumioApiSecret = config('services.jumio.api_secret', 'demo_secret');
    }

    public function initiateIdentityVerification(User $user, array $data = [])
    {
        try {
            // Create verification record
            $verification = Verification::create([
                'user_id' => $user->id,
                'verification_type' => 'identity',
                'status' => 'pending',
                'provider' => 'jumio',
            ]);

            // For demo purposes, we'll simulate the verification process
            if (config('app.env') === 'local' || !$this->jumioApiToken || $this->jumioApiToken === 'demo_token') {
                // Simulate verification process for demo
                $verification->update([
                    'external_id' => 'demo_' . $verification->id,
                    'verification_data' => [
                        'demo' => true,
                        'country' => $data['country'] ?? 'USA',
                        'document_type' => $data['document_type'] ?? 'PASSPORT'
                    ],
                    'status' => 'verified',
                    'verified_at' => now(),
                    'expires_at' => now()->addYears(2),
                ]);

                $this->updateUserTrustScore($user);

                return [
                    'success' => true,
                    'verification_id' => $verification->id,
                    'message' => 'Identity verification completed successfully (Demo Mode)',
                ];
            }

            // Real Jumio API call would go here
            $response = Http::withBasicAuth($this->jumioApiToken, $this->jumioApiSecret)
                ->post($this->jumioApiUrl . '/initiate', [
                    'customerInternalReference' => $user->id,
                    'userReference' => $verification->id,
                    'successUrl' => route('verification.success'),
                    'errorUrl' => route('verification.error'),
                    'callbackUrl' => route('verification.callback'),
                    'enabledFields' => 'idNumber,idFirstName,idLastName,idDob,idExpiry,idUsState,idPersonalNumber,idFaceMatch',
                    'presets' => [
                        'index' => 1,
                        'country' => $data['country'] ?? 'USA',
                        'type' => $data['document_type'] ?? 'PASSPORT'
                    ]
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                $verification->update([
                    'external_id' => $responseData['transactionReference'],
                    'verification_data' => $responseData,
                ]);

                return [
                    'success' => true,
                    'verification_id' => $verification->id,
                    'redirect_url' => $responseData['redirectUrl'],
                ];
            }

            throw new \Exception('Failed to initiate verification: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Verification initiation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleCallback(array $callbackData)
    {
        try {
            $verification = Verification::where('external_id', $callbackData['transactionReference'])
                ->first();

            if (!$verification) {
                throw new \Exception('Verification not found');
            }

            $status = $this->mapJumioStatus($callbackData['transactionStatus']);

            $verification->update([
                'status' => $status,
                'verification_data' => array_merge(
                    $verification->verification_data ?? [],
                    $callbackData
                ),
                'verified_at' => $status === 'verified' ? now() : null,
                'expires_at' => $status === 'verified' ? now()->addYears(2) : null,
                'rejection_reason' => $status === 'rejected' ? $callbackData['rejectReason'] ?? null : null,
            ]);

            // Update user trust score
            if ($status === 'verified') {
                $this->updateUserTrustScore($verification->user);
            }

            return $verification;

        } catch (\Exception $e) {
            Log::error('Verification callback handling failed', [
                'callback_data' => $callbackData,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function initiateStudentVerification(User $user, array $data)
    {
        try {
            $verification = Verification::create([
                'user_id' => $user->id,
                'verification_type' => 'student',
                'status' => 'pending',
                'provider' => 'manual',
                'verification_data' => $data,
            ]);

            // In a real implementation, you would integrate with university APIs
            // or use document verification services

            // For demo purposes, we'll auto-verify if student ID is provided
            if (!empty($data['student_id']) && !empty($data['university'])) {
                $verification->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'expires_at' => now()->addYear(),
                ]);

                $this->updateUserTrustScore($user);
            }

            return $verification;

        } catch (\Exception $e) {
            Log::error('Student verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function mapJumioStatus($jumioStatus)
    {
        return match ($jumioStatus) {
            'DONE' => 'verified',
            'FAILED' => 'rejected',
            'EXPIRED' => 'expired',
            default => 'pending',
        };
    }

    protected function updateUserTrustScore(User $user)
    {
        $verifications = $user->verifications()
            ->where('status', 'verified')
            ->pluck('verification_type')
            ->toArray();

        $score = 0;
        if (in_array('identity', $verifications)) $score += 0.5;
        if (in_array('student', $verifications)) $score += 0.3;
        if (in_array('landlord', $verifications)) $score += 0.2;

        // Update user's properties trust scores
        $user->properties()->update(['trust_score' => $score]);
    }
}
