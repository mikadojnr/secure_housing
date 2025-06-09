<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    {{ substr($recipient->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $recipient->name }}</h2>
                    <p class="text-sm text-gray-600">
                        {{ $recipient->profile && $recipient->profile->user_type === 'landlord' ? 'Landlord' : 'Student' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('messages.index') }}"
               class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Messages -->
    <div class="h-96 overflow-y-auto p-4 space-y-4 custom-scrollbar">
        @forelse($messages as $message)
            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md">
                    <div class="message-bubble {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                        @if($message->is_flagged)
                            <div class="bg-red-100 border border-red-300 rounded p-2 mb-2">
                                <div class="flex items-center text-red-700 text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Flagged as suspicious
                                </div>
                            </div>
                        @endif

                        <p class="text-sm">{{ $message->content }}</p>

                        <div class="flex items-center justify-between mt-2 text-xs opacity-70">
                            <span>{{ $message->created_at->format('M j, g:i A') }}</span>
                            @if($message->sender_id === auth()->id())
                                <span>{{ $message->is_read ? 'Read' : 'Sent' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-gray-500">No messages yet. Start the conversation!</p>
            </div>
        @endforelse
    </div>

    <!-- Message Input -->
    <div class="border-t p-4">
        <form wire:submit="sendMessage" class="flex space-x-3">
            <div class="flex-1">
                <textarea wire:model="newMessage"
                          placeholder="Type your message..."
                          rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                          @keydown.enter.prevent="$wire.sendMessage()"></textarea>
                @error('newMessage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-md transition-colors">
                <span wire:loading.remove>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </span>
                <span wire:loading>
                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </form>

        <!-- Security Warning -->
        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-xs text-yellow-700">
                    <strong>Safety Reminder:</strong> Never share personal financial information or send money outside our platform.
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('warning'))
        <div class="p-4 bg-yellow-50 border-t border-yellow-200">
            <div class="flex items-center text-yellow-700 text-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ session('warning') }}
            </div>
        </div>
    @endif
</div>
