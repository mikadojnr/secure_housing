<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($bookings->count() > 0)
                        <div class="space-y-6">
                            @foreach($bookings as $booking)
                                <div class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('properties.show', $booking->property) }}" class="hover:text-blue-600">
                                                    {{ $booking->property->title }}
                                                </a>
                                            </h3>
                                            <p class="text-gray-600">{{ $booking->property->address }}, {{ $booking->property->city }}</p>
                                        </div>
                                        <div class="text-right">
                                            @if($booking->status === 'pending')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    Confirmed
                                                </span>
                                            @elseif($booking->status === 'cancelled')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                    Cancelled
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <span class="text-sm text-gray-600">Move-in Date</span>
                                            <div class="font-medium">{{ $booking->move_in_date->format('M j, Y') }}</div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Total Amount</span>
                                            <div class="font-medium">${{ number_format($booking->total_amount) }}</div>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Booking Date</span>
                                            <div class="font-medium">{{ $booking->created_at->format('M j, Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="flex space-x-4">
                                        <a href="{{ route('bookings.show', $booking) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                            View Details
                                        </a>
                                        <a href="{{ route('messages.show', ['user' => $booking->property->landlord, 'property_id' => $booking->property->id]) }}"
                                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                            Message Landlord
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $bookings->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h3>
                            <p class="text-gray-500 mb-4">Start exploring properties to make your first booking.</p>
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
</x-app-layout>
