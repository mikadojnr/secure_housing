<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\VerificationService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerificationCenter extends Component
{
    use WithFileUploads;

    public $activeTab = 'identity';
    public $user;
    public $identityVerification;
    public $studentVerification;
    public $country;
    public $document_type;
    public $identity_document_number;
    public $identity_document_front;
    public $identity_document_back;
    public $selfie;
    public $university;
    public $student_id;
    public $enrollment_year;
    public $degree_program;
    public $enrollment_document;
    public $student_id_card;
    public $isSubmitting = false;
    public $message = '';
    public $messageType = 'info';

    protected $rules = [
        'identity_document_front' => 'required|image|max:5120', // 5MB max
        'identity_document_back' => 'nullable|image|max:5120', // Changed to nullable
        'selfie' => 'required|image|max:5120',
        'university' => 'required|string|max:255',
        'student_id' => 'required|string|max:255',
        'enrollment_year' => 'nullable|integer|min:2000|max:2030',
        'degree_program' => 'nullable|string|max:255',
        'enrollment_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        'student_id_card' => 'nullable|image|max:5120',
        'country' => 'required|string|max:255',
        'document_type' => 'required|string|in:international_passport,drivers_license,national_identity_number,voters_card',
        'identity_document_number' => 'required|string|max:255',
    ];

    protected $messages = [
        'identity_document_front.required' => 'Please upload the front of your ID',
        'identity_document_back.required' => 'Please upload the back of your ID',
        'selfie.required' => 'Please upload a selfie',
        'university.required' => 'Please enter your university name',
        'student_id.required' => 'Please enter your student ID', // Fixed typo
        'enrollment_document.required' => 'Please upload your enrollment document',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->identityVerification = $this->user->verifications()
            ->where('verification_type', 'identity')
            ->latest()
            ->first();
        $this->studentVerification = $this->user->verifications()
            ->where('verification_type', 'student')
            ->latest()
            ->first();
        $profile = $this->user->profile;
        if ($profile) {
            $this->university = $profile->university ?? '';
            $this->student_id = $profile->student_id ?? '';
        }
        $this->country = 'Nigeria';
        $this->message = '';
        $this->messageType = 'info';
    }

    public function submitIdentityVerification()
    {
        $this->validate([
            'identity_document_front' => $this->rules['identity_document_front'],
            'identity_document_back' => $this->rules['identity_document_back'],
            'selfie' => $this->rules['selfie'],
            'country' => $this->rules['country'],
            'document_type' => $this->rules['document_type'],
            'identity_document_number' => $this->rules['identity_document_number'],
        ]);

        $this->isSubmitting = true;
        $this->message = '';

        try {
            $verificationService = app(VerificationService::class);
            $verification = $verificationService->initiateIdentityVerification($this->user, [
                'country' => $this->country,
                'document_type' => $this->document_type,
                'identity_document_number' => $this->identity_document_number,
                'identity_document_front' => $this->identity_document_front,
                'identity_document_back' => $this->identity_document_back,
                'selfie' => $this->selfie,
            ]);

            $this->identityVerification = $verification;
            $this->messageType = 'success';
            $this->message = 'Identity verification submitted successfully.';
            $this->reset(['identity_document_front', 'identity_document_back', 'selfie', 'country', 'document_type', 'identity_document_number']);
        } catch (\Exception $e) {
            $this->messageType = 'error';
            $this->message = 'An error occurred while submitting your verification: ' . $e->getMessage();
        }

        $this->isSubmitting = false;
    }

    public function submitStudentVerification()
    {
        $this->validate([
            'university' => $this->rules['university'],
            'student_id' => $this->rules['student_id'],
            'enrollment_year' => $this->rules['enrollment_year'],
            'degree_program' => $this->rules['degree_program'],
            'enrollment_document' => $this->rules['enrollment_document'],
            'student_id_card' => $this->rules['student_id_card'],
        ]);

        $this->isSubmitting = true;
        $this->message = '';

        try {
            $verificationService = app(VerificationService::class);
            $verification = $verificationService->initiateStudentVerification($this->user, [
                'university' => $this->university,
                'student_id' => $this->student_id,
                'enrollment_year' => $this->enrollment_year,
                'degree_program' => $this->degree_program,
                'enrollment_document' => $this->enrollment_document,
                'student_id_card' => $this->student_id_card,
            ]);

            $this->studentVerification = $verification;
            $this->messageType = 'success';
            $this->message = 'Student verification submitted successfully.';
            $this->reset(['university', 'student_id', 'enrollment_year', 'degree_program', 'enrollment_document', 'student_id_card']);
        } catch (\Exception $e) {
            $this->messageType = 'error';
            $this->message = 'An error occurred while submitting your verification: ' . $e->getMessage();
        }

        $this->isSubmitting = false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->message = '';
        $this->messageType = 'info';
    }

    public function render()
    {
        return view('livewire.verification-center', [
            'user' => $this->user,
            'identityVerification' => $this->identityVerification,
            'studentVerification' => $this->studentVerification,
        ])->layout('components.layouts.app');
    }
}
