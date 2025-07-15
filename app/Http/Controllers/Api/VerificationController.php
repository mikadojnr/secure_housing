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
            'country' => 'required|string|max:255',
            'document_type' => 'required|in:international_passport,drivers_license,national_identity_number,voters_card',
            'identity_document_number' => 'required|string|max:255',
            'home_town_address' => 'required|string|max:500',
            'next_of_kin' => 'nullable|string|max:255',
            'identity_document_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identity_document_back' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'selfie' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            // Pass the validated data directly, including file instances from the request
            $result = $this->verificationService->initiateIdentityVerification(
                $user,
                [
                    'country' => $data['country'],
                    'document_type' => $data['document_type'],
                    'identity_document_number' => $data['identity_document_number'],
                    'home_town_address' => $data['home_town_address'],
                    'next_of_kin' => $data['next_of_kin'],
                    'identity_document_front' => $request->file('identity_document_front'),
                    'identity_document_back' => $request->file('identity_document_back'),
                    'selfie' => $request->file('selfie'),
                ]
            );

            // The service now returns the Verification model directly, or throws an exception
            return response()->json([
                'message' => 'Identity verification initiated successfully.',
                'verification_id' => $result->id,
                'status' => $result->status,
            ]);

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
            'enrollment_year' => 'required|integer|min:1900|max:' . (date('Y') + 2),
            'degree_program' => 'required|string',
            'enrollment_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'student_id_card' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Pass the validated data directly, including file instances from the request
            $verification = $this->verificationService->initiateStudentVerification(
                $user,
                [
                    'university' => $data['university'],
                    'student_id' => $data['student_id'],
                    'enrollment_year' => $data['enrollment_year'],
                    'degree_program' => $data['degree_program'],
                    'enrollment_document' => $request->file('enrollment_document'),
                    'student_id_card' => $request->file('student_id_card'),
                ]
            );

            return response()->json([
                'message' => 'Student verification initiated successfully.',
                'verification_id' => $verification->id,
                'status' => $verification->status,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to initiate student verification: ' . $e->getMessage()], 400);
        }
    }

    public function callback(Request $request)
    {
        // This endpoint receives callbacks from external verification providers (e.g., Jumio)
        // It should be publicly accessible and handle the provider's specific payload.
        Log::info('Verification callback received', $request->all());

        try {
            // Assuming handleJumioCallback is the correct method for external callbacks
            $this->verificationService->handleJumioCallback($request); // Pass the full request object

            return response()->json([
                'message' => 'Callback processed successfully.',
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
                    'verification_data' => $v->verification_data, // Include full data for API
                ];
            }),
        ]);
    }
}
