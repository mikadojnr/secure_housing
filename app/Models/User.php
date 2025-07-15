<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            // Automatically create user profile when user is created
            $user->profile()->create([
                'user_type' => 'student', // Default to student
                'preferences' => json_encode([
                    'notifications' => [
                        'email_bookings' => true,
                        'email_messages' => true,
                        'email_reviews' => true,
                        'sms_bookings' => false,
                        'sms_messages' => false,
                    ],
                    'privacy' => [
                        'show_phone' => false,
                        'show_email' => false,
                        'show_university' => true,
                    ],
                    'search' => [
                        'max_price' => 1000,
                        'preferred_areas' => [],
                        'property_types' => [],
                        'amenities' => [],
                    ]
                ])
            ]);
        });
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function UserProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function getUserTypeAttribute()
    {
        return $this->profile->user_type ?? 'student';
    }

    public function isVerified($verificationType = 'identity')
    {
        return $this->verifications()
            ->where('verification_type', $verificationType)
            ->where('status', 'verified')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function getVerifiedTypesAttribute()
    {
        return $this->verifications()
            ->where('status', 'verified')
            ->pluck('verification_type')
            ->toArray();
    }

    public function getVerificationLevel()
    {
        $verifications = $this->verifications()
            ->where('status', 'verified')
            ->pluck('verification_type')
            ->toArray();

        if (in_array('identity', $verifications) && in_array('student', $verifications)) {
            return 'verified';
        } elseif (in_array('identity', $verifications)) {
            return 'partial';
        }

        return 'unverified';
    }

    /**
     * Get the user's trust score based on verifications and activity
     */
    public function getTrustScoreAttribute()
    {
        $score = 0;

        // Base score for email verification
        if ($this->hasVerifiedEmail()) {
            $score += 20;
        }

        // Score for identity verification
        if ($this->isVerified('identity')) {
            $score += 40;
        }

        // Score for student verification
        if ($this->isVerified('student')) {
            $score += 30;
        }

        // Score for having a complete profile
        if ($this->profile && $this->profile->isComplete()) {
            $score += 10;
        }

        return min($score, 100); // Cap at 100
    }

    /**
     * Check if user is a student
     */
    public function isStudent()
    {
        return $this->getUserTypeAttribute() === 'student';
    }

    /**
     * Check if user is a landlord
     */
    public function isLandlord()
    {
        return $this->getUserTypeAttribute() === 'landlord';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->getUserTypeAttribute() === 'admin';
    }

    /**
     * Calculate and update the user's trust score.
     */
    public function updateTrustScore(): void
    {
        $score = 0.0;

        // Base score for email verification
        if ($this->hasVerifiedEmail()) {
            $score += 0.1;
        }

        // Score for profile completeness
        if ($this->profile && $this->profile->isComplete()) {
            $score += 0.1;
        }

        // Score for identity verification
        $identityVerification = $this->verifications()->where('verification_type', 'identity')->where('status', 'verified')->first();
        if ($identityVerification) {
            $score += 0.4; // Significant boost for identity verification
        }

        // Score for student verification
        $studentVerification = $this->verifications()->where('verification_type', 'student')->where('status', 'verified')->first();
        if ($studentVerification) {
            $score += 0.2; // Boost for student verification
        }

        // Score for landlord verification (if applicable and implemented)
        $landlordVerification = $this->verifications()->where('verification_type', 'landlord')->where('status', 'verified')->first();
        if ($landlordVerification) {
            $score += 0.2; // Boost for landlord verification
        }

        // Cap the score at 1.0
        $this->trust_score = min(1.0, $score);
        $this->save();

        Log::info("User {$this->id} trust score updated to: {$this->trust_score}");
    }
}
