<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($conversations->count() > 0)
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                @php
                                    $otherUser = $conversation->sender_id === auth()->id()
                                        ? $conversation->recipient
                                        : $conversation->sender;
                                @endphp
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <a href="{{ route('messages.show', ['user' => $otherUser, 'property_id' => $conversation->property_id]) }}"
                                       class="block">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                    {{ substr($otherUser->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $otherUser->name }}</div>
                                                    @if($conversation->property)
                                                        <div class="text-sm text-gray-600">{{ $conversation->property->title }}</div>
                                                    @endif
                                                    <div class="text-sm text-gray-500 truncate max-w-md">
                                                        {{ Str::limit($conversation->content, 60) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-500">
                                                    {{ $conversation->created_at->diffForHumans() }}
                                                </div>
                                                @if(!$conversation->is_read && $conversation->recipient_id === auth()->id())
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full mt-1 ml-auto"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                            <p class="text-gray-500 mb-4">Start a conversation with a landlord to see messages here.</p>
                            <a href="{{ route('properties.index') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                                Browse Properties
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app>
