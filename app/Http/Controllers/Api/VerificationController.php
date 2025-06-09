<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function initiateIdentityVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => 'required|string|size:3',
            'document_type' => 'required|in:PASSPORT,DRIVING_LICENSE,ID_CARD',
        ]);

        $result = $this->verificationService->initiateIdentityVerification(
            auth()->user(),
            $validated
        );

        if ($result['success']) {
            return response()->json([
                'verification_id' => $result['verification_id'],
                'redirect_url' => $result['redirect_url'],
                'message' => 'Identity verification initiated successfully',
            ]);
        }

        return response()->json([
            'error' => $result['error'],
        ], 400);
    }

    public function initiateStudentVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'university' => 'required|string',
            'student_id' => 'required|string',
            'enrollment_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            // Handle file upload
            $documentPath = $request->file('enrollment_document')
                ->store('verification-documents', 'private');

            $verification = $this->verificationService->initiateStudentVerification(
                auth()->user(),
                [
                    ...$validated,
                    'document_path' => $documentPath,
                ]
            );

            return response()->json([
                'verification_id' => $verification->id,
                'status' => $verification->status,
                'message' => 'Student verification initiated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to initiate student verification',
            ], 400);
        }
    }

    public function callback(Request $request): JsonResponse
    {
        try {
            $verification = $this->verificationService->handleCallback($request->all());

            return response()->json([
                'status' => $verification->status,
                'message' => 'Verification status updated',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to process verification callback',
            ], 400);
        }
    }

    public function status(): JsonResponse
    {
        $user = auth()->user();
        $verifications = $user->verifications()
            ->latest()
            ->get()
            ->groupBy('verification_type');

        return response()->json([
            'verification_level' => $user->getVerificationLevel(),
            'verifications' => $verifications,
        ]);
    }
}
