<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Verification;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Mockery;

class VerificationApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $verificationService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $this->user->id,
            'user_type' => 'student',
        ]);

        $this->verificationService = Mockery::mock(VerificationService::class);
        $this->app->instance(VerificationService::class, $this->verificationService);
    }

    public function test_can_initiate_identity_verification()
    {
        Sanctum::actingAs($this->user);

        $this->verificationService
            ->shouldReceive('initiateIdentityVerification')
            ->once()
            ->with($this->user, [
                'country' => 'USA',
                'document_type' => 'PASSPORT'
            ])
            ->andReturn([
                'success' => true,
                'verification_id' => 1,
                'redirect_url' => 'https://jumio.com/verify/123'
            ]);

        $response = $this->postJson('/api/verification/identity', [
            'country' => 'USA',
            'document_type' => 'PASSPORT'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'verification_id',
                    'redirect_url',
                    'message'
                ]);
    }

    public function test_identity_verification_validation()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/verification/identity', [
            'country' => 'INVALID',
            'document_type' => 'INVALID_TYPE'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['country', 'document_type']);
    }

    public function test_can_initiate_student_verification()
    {
        Storage::fake('private');
        Sanctum::actingAs($this->user);

        $file = UploadedFile::fake()->create('enrollment.pdf', 1000, 'application/pdf');

        $this->verificationService
            ->shouldReceive('initiateStudentVerification')
            ->once()
            ->andReturn(new Verification([
                'id' => 1,
                'status' => 'pending'
            ]));

        $response = $this->postJson('/api/verification/student', [
            'university' => 'Harvard University',
            'student_id' => 'H123456789',
            'enrollment_document' => $file
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'verification_id',
                    'status',
                    'message'
                ]);
    }

    public function test_student_verification_validation()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/verification/student', [
            'university' => '',
            'student_id' => '',
            // Missing enrollment_document
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['university', 'student_id', 'enrollment_document']);
    }

    public function test_can_get_verification_status()
    {
        Sanctum::actingAs($this->user);

        // Create some verification records
        Verification::factory()->create([
            'user_id' => $this->user->id,
            'verification_type' => 'identity',
            'status' => 'verified'
        ]);

        Verification::factory()->create([
            'user_id' => $this->user->id,
            'verification_type' => 'student',
            'status' => 'pending'
        ]);

        $response = $this->getJson('/api/verification/status');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'verification_level',
                    'verifications'
                ]);
    }

    public function test_verification_callback_processing()
    {
        $verification = Verification::factory()->create([
            'user_id' => $this->user->id,
            'verification_type' => 'identity',
            'status' => 'pending',
            'external_id' => 'jumio_123'
        ]);

        $this->verificationService
            ->shouldReceive('handleCallback')
            ->once()
            ->with([
                'transactionReference' => 'jumio_123',
                'transactionStatus' => 'DONE'
            ])
            ->andReturn($verification);

        $response = $this->postJson('/api/verification/callback', [
            'transactionReference' => 'jumio_123',
            'transactionStatus' => 'DONE'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message'
                ]);
    }

    public function test_unauthenticated_user_cannot_access_verification_endpoints()
    {
        $response = $this->postJson('/api/verification/identity', [
            'country' => 'USA',
            'document_type' => 'PASSPORT'
        ]);

        $response->assertStatus(401);

        $response = $this->getJson('/api/verification/status');
        $response->assertStatus(401);
    }
}
