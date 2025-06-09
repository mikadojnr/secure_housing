<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verification_type',
        'status',
        'provider',
        'external_id',
        'verification_data',
        'rejection_reason',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'verification_data' => 'array',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid()
    {
        return $this->status === 'verified' && !$this->isExpired();
    }
}
