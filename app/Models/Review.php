<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'reviewer_id',
        'booking_id',
        'rating',
        'comment',
        'ratings_breakdown',
        'is_verified',
    ];

    protected $casts = [
        'ratings_breakdown' => 'array',
        'is_verified' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
