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
            'country' => 'required|string|max:255',
            'document_type' => 'required|in:international_passport,drivers_license,national_identity_number,voters_card',
            'identity_document_number' => 'required|string|max:255',
            'identity_document_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identity_document_back' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'selfie' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $verification = $this->verificationService->initiateIdentityVerification($user, $data);
            return redirect()->route('verification.success')->with('success', 'Identity verification initiated successfully.');
        } catch (\Exception $e) {
            Log::error('Identity verification failed', ['error' => $e->getMessage()]);
            return redirect()->route('verification.error')->with('error', 'Failed to initiate identity verification: ' . $e->getMessage());
        }
    }

    public function initiateStudentVerification(Request $request)
    {
        $user = Auth::user();
        if ($user->profile->user_type !== 'student') {
            return redirect()->route('verification.error')->with('error', 'Student verification is only available for student accounts.');
        }

        $data = $request->validate([
            'university' => 'required|string|max:255',
            'student_id' => 'required|string|max:255',
            'enrollment_year' => 'nullable|integer|min:2000|max:2030',
            'degree_program' => 'nullable|string|max:255',
            'enrollment_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'student_id_card' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $verification = $this->verificationService->initiateStudentVerification($user, $data);
            return redirect()->route('verification.success')->with('success', 'Student verification submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Student verification failed', ['error' => $e->getMessage()]);
            return redirect()->route('verification.error')->with('error', 'Failed to submit student verification: ' . $e->getMessage());
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
        Log::info('Verification callback received (Web Controller)', $request->all());

        try {
            $this->verificationService->handleJumioCallback($request);
            return response()->json([
                'message' => 'Verification status updated via callback',
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing verification callback (Web Controller): ' . $e->getMessage(), ['payload' => $request->all()]);
            return response()->json([
                'error' => 'Failed to process verification callback',
            ], 400);
        }
    }
}
