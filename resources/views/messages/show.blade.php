<x-app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Conversation with') }} {{ $user->name }}
                @if($property)
                    <span class="text-sm text-gray-600 font-normal">about {{ $property->title }}</span>
                @endif
            </h2>
            <a href="{{ route('messages.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @if($property)
                    <!-- Property Context -->
                    <div class="bg-gray-50 border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-4">
                            @if($property->images->count() > 0)
                                <img src="{{ Storage::url($property->images->first()->image_path) }}"
                                     alt="{{ $property->title }}"
                                     class="w-16 h-16 object-cover rounded-lg">
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $property->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $property->address }}, {{ $property->city }}</p>
                                <p class="text-sm font-medium text-green-600">${{ number_format($property->rent_amount) }}/month</p>
                            </div>
                            <div class="ml-auto">
                                <a href="{{ route('properties.show', $property) }}"
                                   class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    View Property →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Messages Container -->
                <div class="h-96 overflow-y-auto p-6 border-b border-gray-200" id="messages-container">
                    @if($messages->count() > 0)
                        <div class="space-y-4">
                            @foreach($messages as $message)
                                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                                        <p class="text-sm">{{ $message->content }}</p>
                                        <div class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-indigo-200' : 'text-gray-500' }}">
                                            {{ $message->created_at->format('M j, g:i A') }}
                                            @if($message->is_flagged)
                                                <span class="ml-2 text-red-400">⚠️ Flagged</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No messages yet. Start the conversation!</p>
                        </div>
                    @endif
                </div>

                <!-- Message Input -->
                <div class="p-6">
                    <form method="POST" action="{{ route('messages.store') }}" class="flex space-x-4">
                        @csrf
                        <input type="hidden" name="recipient_id" value="{{ $user->id }}">
                        @if($propertyId)
                            <input type="hidden" name="property_id" value="{{ $propertyId }}">
                        @endif

                        <div class="flex-1">
                            <textarea name="content"
                                      rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Type your message..."
                                      required></textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex-shrink-0">
                            <button type="submit"
                                    class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200">
                                Send
                            </button>
                        </div>
                    </form>

                    <!-- Security Warning -->
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Security Reminder</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside">
                                        <li>Never share personal financial information</li>
                                        <li>All payments should go through our secure platform</li>
                                        <li>Report suspicious messages immediately</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;
        });
    </script>
</x-app>
