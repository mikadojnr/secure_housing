<?php

namespace App\Http\Controllers;

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
                return redirect()->route('verification.success')->with('success', $result['message'] ?? 'Identity verification initiated successfully.');
            }

            return redirect()->route('verification.error')->with('error', $result['error']);
        } catch (\Exception $e) {
            Log::error('Identity verification failed', ['error' => $e->getMessage()]);
            return redirect()->route('verification.error')->with('error', 'Failed to initiate identity verification.');
        }
    }

    public function initiateStudentVerification(Request $request)
    {
        $user = Auth::user();
        if ($user->profile->user_type !== 'student') {
            return redirect()->route('verification.error')->with('error', 'Student verification is only available for student accounts.');
        }

        $data = $request->validate([
            'university' => 'required|string',
            'student_id' => 'required|string',
            'enrollment_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            $documentPath = $request->file('enrollment_document')
                ->store('verification-documents', 'private');

            $verification = $this->verificationService->initiateStudentVerification($user, [
                'university' => $data['university'],
                'student_id' => $data['student_id'],
                'document_path' => $documentPath,
            ]);

            return redirect()->route('verification.success')->with('success', 'Student verification submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Student verification failed', ['error' => $e->getMessage()]);
            return redirect()->route('verification.error')->with('error', 'Failed to submit student verification.');
        }
    }

    public function success(Request $request)
    {
        return view('verification.success');
    }

    public function error(Request $request)
    {
        return view('verification.error');
    }

    public function callback(Request $request)
    {
        try {
            $verification = $this->verificationService->handleCallback($request->all());

            return response()->json([
                'status' => 'success',
                'verification_status' => $verification->status,
            ]);
        } catch (\Exception $e) {
            Log::error('Verification callback failed', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process verification callback',
            ], 400);
        }
    }
}
