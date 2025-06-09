<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 font-montserrat">Booking Details</h1>
                        <p class="text-gray-600 mt-1">Booking #{{ $booking->id }}</p>
                    </div>
                    <div class="text-right">
                        @if($booking->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Pending Approval
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
            </div>

            <div class="p-6">
                <!-- Property Information -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Property Information</h3>
                    <div class="flex items-start space-x-4">
                        @if($booking->property->primaryImage)
                            <img src="{{ Storage::url($booking->property->primaryImage->image_path) }}"
                                 alt="{{ $booking->property->title }}"
                                 class="w-20 h-20 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $booking->property->title }}</h4>
                            <p class="text-gray-600 text-sm">{{ $booking->property->address }}, {{ $booking->property->city }}</p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-lg font-bold text-blue-600">${{ number_format($booking->property->rent_amount) }}/month</span>
                                <span class="text-sm text-gray-500">{{ $booking->property->bedrooms }}bd • {{ $booking->property->bathrooms }}ba</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Booking Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Move-in Date</span>
                                <div class="font-medium">{{ $booking->move_in_date->format('M j, Y') }}</div>
                            </div>
                            @if($booking->move_out_date)
                                <div>
                                    <span class="text-sm text-gray-600">Move-out Date</span>
                                    <div class="font-medium">{{ $booking->move_out_date->format('M j, Y') }}</div>
                                </div>
                            @endif
                            <div>
                                <span class="text-sm text-gray-600">Booking Date</span>
                                <div class="font-medium">{{ $booking->created_at->format('M j, Y') }}</div>
                            </div>
                            @if($booking->confirmed_at)
                                <div>
                                    <span class="text-sm text-gray-600">Confirmed Date</span>
                                    <div class="font-medium">{{ $booking->confirmed_at->format('M j, Y') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Payment Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Amount</span>
                                <span class="font-medium">${{ number_format($booking->total_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Security Deposit</span>
                                <span class="font-medium">${{ number_format($booking->deposit_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status</span>
                                <span class="font-medium capitalize">{{ str_replace('_', ' ', $booking->payment_status) }}</span>
                            </div>
                            @if($booking->escrow_transaction_id)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transaction ID</span>
                                    <span class="font-medium font-mono text-sm">{{ $booking->escrow_transaction_id }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Landlord Information -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Landlord Contact</h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            {{ substr($booking->property->landlord->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $booking->property->landlord->name }}</div>
                            <div class="text-sm text-gray-600">{{ $booking->property->landlord->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('messages.show', ['user' => $booking->property->landlord, 'property_id' => $booking->property->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Send Message
                        </a>
                    </div>
                </div>

                <!-- Actions -->
                @if($booking->status === 'pending' && auth()->id() === $booking->property->landlord_id)
                    <div class="flex space-x-4">
                        <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                                Confirm Booking
                            </button>
                        </form>
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition-colors"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                Cancel Booking
                            </button>
                        </form>
                    </div>
                @elseif($booking->status === 'pending' && auth()->id() === $booking->student_id)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-yellow-800">Waiting for Landlord Approval</h4>
                                <p class="text-yellow-700 text-sm">Your booking request is pending approval from the landlord.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
