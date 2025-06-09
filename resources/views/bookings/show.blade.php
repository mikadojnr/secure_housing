<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Status Banner -->
                    <div class="mb-6 p-4 rounded-lg {{ $booking->status === 'confirmed' ? 'bg-green-50 border border-green-200' : ($booking->status === 'cancelled' ? 'bg-red-50 border border-red-200' : ($booking->status === 'completed' ? 'bg-blue-50 border border-blue-200' : 'bg-yellow-50 border border-yellow-200')) }}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($booking->status === 'confirmed')
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($booking->status === 'cancelled')
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($booking->status === 'completed')
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium {{ $booking->status === 'confirmed' ? 'text-green-800' : ($booking->status === 'cancelled' ? 'text-red-800' : ($booking->status === 'completed' ? 'text-blue-800' : 'text-yellow-800')) }}">
                                    Booking {{ ucfirst($booking->status) }}
                                </h3>
                                <div class="mt-1 text-sm {{ $booking->status === 'confirmed' ? 'text-green-700' : ($booking->status === 'cancelled' ? 'text-red-700' : ($booking->status === 'completed' ? 'text-blue-700' : 'text-yellow-700')) }}">
                                    @if($booking->status === 'pending')
                                        Your booking request is pending landlord approval.
                                    @elseif($booking->status === 'confirmed')
                                        Your booking has been confirmed! Payment processing will begin soon.
                                    @elseif($booking->status === 'cancelled')
                                        This booking has been cancelled.
                                        @if($booking->cancellation_reason)
                                            <p class="mt-1">Reason: {{ $booking->cancellation_reason }}</p>
                                        @endif
                                    @elseif($booking->status === 'completed')
                                        This booking has been completed.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Property Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Details</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start space-x-4">
                                    @if($booking->property->images->count() > 0)
                                        <img src="{{ Storage::url($booking->property->images->where('is_primary', true)->first()?->image_path ?? $booking->property->images->first()->image_path) }}"
                                             alt="{{ $booking->property->title }}"
                                             class="w-20 h-20 object-cover rounded-lg">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $booking->property->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $booking->property->address }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->property->city }}, {{ $booking->property->state }} {{ $booking->property->postal_code }}</p>
                                        <div class="mt-2">
                                            <span class="text-sm text-gray-500">{{ $booking->property->bedrooms }} bed • {{ $booking->property->bathrooms }} bath</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('properties.show', $booking->property) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View Property Details →
                                    </a>
                                </div>
                            </div>

                            <!-- Landlord Information -->
                            <div class="mt-6">
                                <h4 class="font-medium text-gray-900 mb-2">Landlord</h4>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                                        <img src="{{ $booking->property->landlord->profile_photo_url }}" alt="{{ $booking->property->landlord->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $booking->property->landlord->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $booking->property->landlord->email }}</div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('messages.show', ['user' => $booking->property->landlord->id, 'property_id' => $booking->property->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Message Landlord →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Information</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Move-in Date</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $booking->move_in_date->format('M d, Y') }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Move-out Date</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $booking->move_out_date->format('M d, Y') }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lease Duration</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $booking->duration_in_days }} days ({{ $booking->duration_in_months }} months)</div>
                                </div>

                                @if($booking->special_requests)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Special Requests</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $booking->special_requests }}</div>
                                    </div>
                                @endif

                                <!-- Cost Breakdown -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium text-gray-900 mb-3">Cost Breakdown</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Monthly Rent:</span>
                                            <span class="text-sm font-medium">${{ number_format($booking->property->rent_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Security Deposit:</span>
                                            <span class="text-sm font-medium">${{ number_format($booking->deposit_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Total Rent ({{ $booking->duration_in_days }} days):</span>
                                            <span class="text-sm font-medium">${{ number_format($booking->total_amount, 2) }}</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="flex justify-between">
                                            <span class="font-medium text-gray-900">Total Amount:</span>
                                            <span class="font-medium text-indigo-600">${{ number_format($booking->total_due, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $booking->payment_status_badge_class }}">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Payment Button (for students with confirmed bookings) -->
                                @if(auth()->user()->user_type === 'student' && $booking->student_id === auth()->id() && $booking->status === 'confirmed' && $booking->payment_status === 'pending')
                                    <div class="mt-4">
                                        <a href="#payment-modal"
                                           class="block w-full bg-indigo-600 text-white text-center py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200"
                                           onclick="document.getElementById('payment-modal').classList.remove('hidden');">
                                            Make Payment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
                        <div class="text-sm text-gray-500">
                            Booking ID: #{{ $booking->id }} • Created {{ $booking->created_at->diffForHumans() }}
                        </div>

                        <div class="flex space-x-3">
                            @if($booking->status === 'pending')
                                @if(auth()->user()->user_type === 'landlord' && auth()->id() === $booking->property->landlord_id)
                                    <form method="POST" action="{{ route('bookings.confirm', $booking) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200"
                                                onclick="return confirm('Are you sure you want to confirm this booking?')">
                                            Confirm Booking
                                        </button>
                                    </form>
                                @endif

                                <button type="button"
                                        class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-200"
                                        onclick="document.getElementById('cancel-modal').classList.remove('hidden');">
                                    Cancel Booking
                                </button>
                            @elseif($booking->status === 'confirmed')
                                @if(auth()->user()->user_type === 'landlord' && auth()->id() === $booking->property->landlord_id)
                                    <form method="POST" action="{{ route('bookings.complete', $booking) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200"
                                                onclick="return confirm('Are you sure you want to mark this booking as completed?')">
                                            Mark as Completed
                                        </button>
                                    </form>
                                @endif

                                <button type="button"
                                        class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-200"
                                        onclick="document.getElementById('cancel-modal').classList.remove('hidden');">
                                    Cancel Booking
                                </button>
                            @endif

                            <a href="{{ route('bookings.index') }}"
                               class="bg-gray-100 text-gray-900 py-2 px-4 rounded-md hover:bg-gray-200 transition duration-200">
                                Back to Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancel-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Booking</h3>
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Cancellation</label>
                    <textarea id="reason" name="reason" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            class="bg-gray-100 text-gray-900 py-2 px-4 rounded-md hover:bg-gray-200 transition duration-200"
                            onclick="document.getElementById('cancel-modal').classList.add('hidden');">
                        Close
                    </button>
                    <button type="submit"
                            class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-200">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Make Payment</h3>
            <form method="POST" action="{{ route('bookings.payment', $booking) }}">
                @csrf
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select payment method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <div id="credit-card-fields" class="space-y-4 hidden">
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="expiry_month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                            <input type="text" id="expiry_month" name="expiry_month" placeholder="MM" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="expiry_year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <input type="text" id="expiry_year" name="expiry_year" placeholder="YY" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-900">Total Amount:</span>
                        <span class="font-medium text-indigo-600">${{ number_format($booking->total_due, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                            class="bg-gray-100 text-gray-900 py-2 px-4 rounded-md hover:bg-gray-200 transition duration-200"
                            onclick="document.getElementById('payment-modal').classList.add('hidden');">
                        Close
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200">
                        Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payment_method');
            const creditCardFields = document.getElementById('credit-card-fields');

            if (paymentMethod && creditCardFields) {
                paymentMethod.addEventListener('change', function() {
                    if (this.value === 'credit_card') {
                        creditCardFields.classList.remove('hidden');
                    } else {
                        creditCardFields.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app>
