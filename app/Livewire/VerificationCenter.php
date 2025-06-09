<?php

namespace App\Livewire;

use App\Services\VerificationService;
use Livewire\Component;
use Livewire\WithFileUploads;

class VerificationCenter extends Component
{
    use WithFileUploads;

    public $activeTab = 'identity';
    public $country = 'USA';
    public $documentType = 'PASSPORT';
    public $university = '';
    public $studentId = '';
    public $enrollmentDocument;
    public $isLoading = false;
    public $identityDocument;

    protected $layout = 'components.layouts.app';

    protected $verificationService;

    public function boot(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function mount()
    {
        // Set default tab based on user type
        if (auth()->user()->user_type === 'student') {
            $this->activeTab = 'identity';
        } else {
            $this->activeTab = 'identity';
        }
    }

    public function initiateIdentityVerification()
    {
        $this->validate([
            'country' => 'required|string|size:3',
            'documentType' => 'required|in:PASSPORT,DRIVING_LICENSE,ID_CARD',
            'identityDocument' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $this->isLoading = true;

        try {
            $documentPath = $this->identityDocument->store('verification-documents', 'private');

            $result = $this->verificationService->initiateIdentityVerification(
                auth()->user(),
                [
                    'country' => $this->country,
                    'document_type' => $this->documentType,
                    'document_path' => $documentPath,
                ]
            );

            $this->isLoading = false;
            $this->reset(['identityDocument']);

            if ($result['success']) {
                session()->flash('success', $result['message'] ?? 'Identity verification submitted successfully!');
                $this->dispatch('verification-updated');
            } else {
                session()->flash('error', $result['error']);
            }
        } catch (\Exception $e) {
            $this->isLoading = false;
            session()->flash('error', 'Failed to submit identity verification.');
        }
    }

    public function initiateStudentVerification()
    {
        // Only allow students to access this
        if (auth()->user()->user_type !== 'student') {
            session()->flash('error', 'Student verification is only available for student accounts.');
            return;
        }

        $this->validate([
            'university' => 'required|string',
            'studentId' => 'required|string',
            'enrollmentDocument' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $this->isLoading = true;

        try {
            $documentPath = $this->enrollmentDocument->store('verification-documents', 'private');

            $verification = $this->verificationService->initiateStudentVerification(
                auth()->user(),
                [
                    'university' => $this->university,
                    'student_id' => $this->studentId,
                    'document_path' => $documentPath,
                ]
            );

            $this->isLoading = false;
            $this->reset(['university', 'studentId', 'enrollmentDocument']);

            session()->flash('success', 'Student verification submitted successfully!');
            $this->dispatch('verification-updated');

        } catch (\Exception $e) {
            $this->isLoading = false;
            session()->flash('error', 'Failed to submit student verification.');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $verifications = $user->verifications()
            ->latest()
            ->get()
            ->groupBy('verification_type');

        return view('livewire.verification-center', [
            'verifications' => $verifications,
            'verificationLevel' => $user->getVerificationLevel(),
        ]);
    }
}
