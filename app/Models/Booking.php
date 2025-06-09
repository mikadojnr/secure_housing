<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'student_id',
        'move_in_date',
        'move_out_date',
        'total_amount',
        'deposit_amount',
        'status',
        'payment_status',
        'escrow_transaction_id',
        'contract_terms',
        'confirmed_at',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'contract_terms' => 'array',
        'confirmed_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function canBeReviewed()
    {
        return $this->status === 'completed' && !$this->review;
    }
}
