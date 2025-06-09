<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'special_requests',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'contract_terms' => 'array',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function canBeReviewed()
    {
        return $this->status === 'completed' && !$this->review;
    }

    public function getDurationInDaysAttribute()
    {
        return $this->move_in_date->diffInDays($this->move_out_date);
    }

    public function getDurationInMonthsAttribute()
    {
        return $this->move_in_date->diffInMonths($this->move_out_date);
    }

    public function getTotalDueAttribute()
    {
        return $this->total_amount + $this->deposit_amount;
    }

    public function getStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-purple-100 text-purple-800',
        ][$this->payment_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
