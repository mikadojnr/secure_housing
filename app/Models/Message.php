<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'property_id',
        'content',
        'is_encrypted',
        'is_read',
        'is_flagged',
        'attachments',
        'read_at',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_read' => 'boolean',
        'is_flagged' => 'boolean',
        'attachments' => 'array',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
