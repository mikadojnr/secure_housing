<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function initiateIdentityVerification(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'country' => 'required|string|size:3',
            'document_type' => 'required|in:PASSPORT,DRIVING_LICENSE,ID_CARD',
            'identity_document_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identity_document_back' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identity_document_number' => 'required|string|max:255',
            'selfie' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'home_town_address' => 'required|string|max:255',
            'next_of_kin' => 'required|string|max:255',
        ]);

        try {
            $documentFrontPath = $request->file('identity_document_front')
                ->store('verification-documents', 'private');
            $documentBackPath = $request->file('identity_document_back')
                ? $request->file('identity_document_back')->store('verification-documents', 'private')
                : null;
            $selfiePath = $request->file('selfie')
                ->store('verification-documents', 'private');

            $result = $this->verificationService->initiateIdentityVerification(
                $user,
                [
                    'country' => $data['country'],
                    'document_type' => $data['document_type'],
                    'identity_document_front_path' => $documentFrontPath,
                    'identity_document_back_path' => $documentBackPath,
                    'identity_document_number' => $data['identity_document_number'],
                    'selfie_path' => $selfiePath,
                    'home_town_address' => $data['home_town_address'],
                    'next_of_kin' => $data['next_of_kin'],
                ]
            );

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'] ?? 'Identity verification initiated successfully.',
                    'verification_id' => $result['verification_id'],
                    'redirect_url' => $result['redirect_url'] ?? null,
                ]);
            }

            return response()->json(['error' => $result['error']], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to initiate identity verification: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function initiateStudentVerification(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'university' => 'required|string',
            'student_id' => 'required|string',
            'enrollment_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            // Handle file upload
            $documentPath = $request->file('enrollment_document')
                ->store('verification-documents', 'private');

            $verification = $this->verificationService->initiateStudentVerification($user, $data);

            return response()->json([
                'message' => 'Student verification initiated successfully.',
                'verification_id' => $verification->id,
                'status' => $verification->status,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to initiate student verification'], 400);
        }
    }

    public function callback(Request $request)
    {
        // This endpoint receives callbacks from external verification providers (e.g., Jumio)
        // It should be publicly accessible and handle the provider's specific payload.
        Log::info('Verification callback received', $request->all());

        try {
            $verification = $this->verificationService->handleCallback($request->all());

            return response()->json([
                'message' => 'Callback processed successfully.',
                'verification_id' => $verification->id,
                'status' => $verification->status,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing verification callback: ' . $e->getMessage(), ['payload' => $request->all()]);
            return response()->json(['error' => 'Failed to process callback.'], 500);
        }
    }

    public function status(Request $request)
    {
        $user = Auth::user();
        $verifications = $user->verifications()->get();

        return response()->json([
            'verification_level' => $user->getVerificationLevel(),
            'verifications' => $verifications->map(function ($v) {
                return [
                    'type' => $v->verification_type,
                    'status' => $v->status,
                    'provider' => $v->provider,
                    'verified_at' => $v->verified_at,
                    'expires_at' => $v->expires_at,
                    'rejection_reason' => $v->rejection_reason,
                ];
            }),
        ]);
    }
}
