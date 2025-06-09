<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get unique conversations
        $conversationUsers = Message::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->select('sender_id', 'recipient_id', 'property_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($message) use ($userId) {
                $otherUserId = $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;
                return $otherUserId . '-' . $message->property_id;
            });

        $conversations = collect();

        foreach ($conversationUsers as $conv) {
            $otherUserId = $conv->sender_id === $userId ? $conv->recipient_id : $conv->sender_id;

            $latestMessage = Message::where(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $userId)->where('recipient_id', $otherUserId);
            })->orWhere(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $otherUserId)->where('recipient_id', $userId);
            })
            ->when($conv->property_id, function ($query) use ($conv) {
                $query->where('property_id', $conv->property_id);
            })
            ->with(['sender', 'recipient', 'property'])
            ->latest()
            ->first();

            if ($latestMessage) {
                $conversations->push($latestMessage);
            }
        }

        return view('messages.index', compact('conversations'));
    }

    public function show(User $user, Request $request)
    {
        $propertyId = $request->get('property');
        $currentUserId = Auth::id();

        $messages = Message::where(function ($query) use ($currentUserId, $user) {
            $query->where('sender_id', $currentUserId)->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($currentUserId, $user) {
            $query->where('sender_id', $user->id)->where('recipient_id', $currentUserId);
        })
        ->when($propertyId, function ($query) use ($propertyId) {
            $query->where('property_id', $propertyId);
        })
        ->with(['sender', 'recipient', 'property'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('recipient_id', $currentUserId)
            ->where('is_read', false)
            ->when($propertyId, function ($query) use ($propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->update(['is_read' => true, 'read_at' => now()]);

        $property = $propertyId ? Property::find($propertyId) : null;

        return view('messages.show', compact('messages', 'user', 'property'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'content' => 'required|string|max:1000',
        ]);

        $validated['sender_id'] = Auth::id();
        $validated['is_read'] = false;

        $message = Message::create($validated);

        if ($request->expectsJson()) {
            return response()->json($message->load(['sender', 'recipient', 'property']));
        }

        return back()->with('success', 'Message sent successfully!');
    }
}
