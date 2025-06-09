<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MessageThread extends Component
{
    public $recipient;
    public $propertyId;
    public $messages = [];
    public $newMessage = '';

    public function mount(User $recipient, $propertyId = null)
    {
        $this->recipient = $recipient;
        $this->propertyId = $propertyId;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = Message::where(function ($query) {
                $query->where('sender_id', Auth::id())
                      ->where('recipient_id', $this->recipient->id);
            })
            ->orWhere(function ($query) {
                $query->where('sender_id', $this->recipient->id)
                      ->where('recipient_id', Auth::id());
            })
            ->when($this->propertyId, function ($query) {
                return $query->where('property_id', $this->propertyId);
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $this->recipient->id)
            ->where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:2000',
        ]);

        // Basic scam detection
        $scamPhrases = [
            'wire transfer', 'western union', 'moneygram', 'bitcoin',
            'urgent payment', 'send money', 'cash only', 'no questions asked'
        ];

        $isScam = false;
        foreach ($scamPhrases as $phrase) {
            if (stripos($this->newMessage, $phrase) !== false) {
                $isScam = true;
                break;
            }
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $this->recipient->id,
            'property_id' => $this->propertyId,
            'content' => $this->newMessage,
            'is_flagged' => $isScam,
        ]);

        if ($isScam) {
            session()->flash('warning', 'Your message contains phrases that may be flagged as suspicious.');
        }

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.message-thread');
    }
}
