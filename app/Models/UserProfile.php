<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'phone',
        'date_of_birth',
        'university',
        'student_id',
        'bio',
        'avatar',
        'preferences',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the profile is complete
     */
    public function isComplete()
    {
        $requiredFields = ['phone', 'date_of_birth'];

        // Add user type specific required fields
        if ($this->user_type === 'student') {
            $requiredFields[] = 'university';
            $requiredFields[] = 'student_id';
        }

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return !empty($this->bio);
    }

    /**
     * Get the user's age
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return null;
        }

        // Basic phone formatting - you can enhance this based on your needs
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s',
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6)
            );
        }

        return $this->phone;
    }

    /**
     * Get user preferences with defaults
     */
    public function getPreferencesAttribute($value)
    {
        $defaults = [
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
        ];

        $preferences = json_decode($value, true) ?? [];

        return array_merge_recursive($defaults, $preferences);
    }

    /**
     * Set preferences ensuring proper structure
     */
    public function setPreferencesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['preferences'] = json_encode($value);
        } else {
            $this->attributes['preferences'] = $value;
        }
    }

    /**
     * Get avatar URL with fallback
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate a default avatar based on user's name
        $name = $this->user->name ?? 'User';
        $initials = collect(explode(' ', $name))->map(function ($word) {
            return strtoupper(substr($word, 0, 1));
        })->take(2)->implode('');

        return "https://ui-avatars.com/api/?name={$initials}&background=3B82F6&color=ffffff&size=200";
    }

    /**
     * Scope for students only
     */
    public function scopeStudents($query)
    {
        return $query->where('user_type', 'student');
    }

    /**
     * Scope for landlords only
     */
    public function scopeLandlords($query)
    {
        return $query->where('user_type', 'landlord');
    }

    /**
     * Scope for complete profiles
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('phone')
                    ->whereNotNull('date_of_birth')
                    ->whereNotNull('bio');
    }
}
