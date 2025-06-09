<?php

namespace App\Http\Controllers;

use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
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
            \Log::error('Verification callback failed', [
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
