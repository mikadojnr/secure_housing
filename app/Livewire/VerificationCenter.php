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
    public $identityDocumentFront;
    public $identityDocumentBack;
    public $identityDocumentNumber;
    public $selfie;
    public $homeTownAddress;
    public $nextOfKin;
    public $university = '';
    public $studentId = '';
    public $enrollmentDocument;
    public $isLoading = false;

    protected $layout = 'components.layouts.app';
    protected $verificationService;

    public function boot(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function mount()
    {
        if (auth()->user()->profile && auth()->user()->profile->user_type === 'student') {
            $this->activeTab = 'student';
        } else {
            $this->activeTab = 'identity';
        }
    }

    protected function rules()
    {
        return [
            'country' => 'required|string|size:3',
            'documentType' => 'required|in:PASSPORT,DRIVING_LICENSE,ID_CARD',
            'identityDocumentFront' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identityDocumentBack' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identityDocumentNumber' => 'required|string|max:255',
            'selfie' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'homeTownAddress' => 'required|string|max:255',
            'nextOfKin' => 'required|string|max:255',
            'university' => 'required_if:activeTab,student|string',
            'studentId' => 'required_if:activeTab,student|string',
            'enrollmentDocument' => 'required_if:activeTab,student|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function initiateIdentityVerification()
    {
        $this->validate();

        $this->isLoading = true;

        try {
            $documentFrontPath = $this->identityDocumentFront->store('verification-documents', 'private');
            $documentBackPath = $this->identityDocumentBack ? $this->identityDocumentBack->store('verification-documents', 'private') : null;
            $selfiePath = $this->selfie->store('verification-documents', 'private');

            $result = $this->verificationService->initiateIdentityVerification(
                auth()->user(),
                [
                    'country' => $this->country,
                    'document_type' => $this->documentType,
                    'identity_document_front_path' => $documentFrontPath,
                    'identity_document_back_path' => $documentBackPath,
                    'identity_document_number' => $this->identityDocumentNumber,
                    'selfie_path' => $selfiePath,
                    'home_town_address' => $this->homeTownAddress,
                    'next_of_kin' => $this->nextOfKin,
                ]
            );

            $this->isLoading = false;
            $this->reset([
                'identityDocumentFront',
                'identityDocumentBack',
                'identityDocumentNumber',
                'selfie',
                'homeTownAddress',
                'nextOfKin',
            ]);

            if ($result['success']) {
                session()->flash('success', $result['message'] ?? 'Identity verification submitted successfully!');
                if (isset($result['redirect_url'])) {
                    return redirect()->to($result['redirect_url']);
                }
                $this->dispatch('verification-updated');
            } else {
                session()->flash('error', $result['error']);
            }
        } catch (\Exception $e) {
            $this->isLoading = false;
            session()->flash('error', 'Failed to submit identity verification: ' . $e->getMessage());
        }
    }

    public function initiateStudentVerification()
    {
        $this->validate();

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
            session()->flash('error', 'Failed to submit student verification: ' . $e->getMessage());
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
