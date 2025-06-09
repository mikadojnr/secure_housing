<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Property') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Property Summary -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-start space-x-4">
                            @if($property->images->count() > 0)
                                <img src="{{ Storage::url($property->images->where('is_primary', true)->first()?->image_path ?? $property->images->first()->image_path) }}"
                                     alt="{{ $property->title }}"
                                     class="w-24 h-24 object-cover rounded-lg">
                            @else
                                <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $property->title }}</h3>
                                <p class="text-gray-600">{{ $property->address }}, {{ $property->city }}, {{ $property->state }}</p>
                                <div class="mt-2 flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">{{ $property->bedrooms }} bed • {{ $property->bathrooms }} bath</span>
                                    <span class="text-lg font-bold text-indigo-600">${{ number_format($property->rent_amount) }}/month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="move_in_date" class="block text-sm font-medium text-gray-700">Move-in Date</label>
                                <input type="date"
                                       id="move_in_date"
                                       name="move_in_date"
                                       value="{{ old('move_in_date', $property->available_from->format('Y-m-d')) }}"
                                       min="{{ $property->available_from->format('Y-m-d') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                @error('move_in_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="move_out_date" class="block text-sm font-medium text-gray-700">Move-out Date</label>
                                <input type="date"
                                       id="move_out_date"
                                       name="move_out_date"
                                       value="{{ old('move_out_date') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                @error('move_out_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="special_requests" class="block text-sm font-medium text-gray-700">Special Requests (Optional)</label>
                            <textarea id="special_requests"
                                      name="special_requests"
                                      rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Any special requests or questions for the landlord...">{{ old('special_requests') }}</textarea>
                            @error('special_requests')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-3">Cost Breakdown</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Monthly Rent:</span>
                                    <span class="text-sm font-medium">${{ number_format($property->rent_amount) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Security Deposit:</span>
                                    <span class="text-sm font-medium">${{ number_format($property->deposit_amount) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lease Duration:</span>
                                    <span class="text-sm font-medium" id="lease-duration">-- days</span>
                                </div>
                                <hr class="my-2">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900">Total Rent:</span>
                                    <span class="font-medium text-indigo-600" id="total-rent">$--</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900">Total Due at Signing:</span>
                                    <span class="font-medium text-indigo-600" id="total-due">${{ number_format($property->deposit_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Important Information</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>This booking request will be sent to the landlord for approval</li>
                                            <li>Payment will be processed only after landlord confirmation</li>
                                            <li>All payments are secured through our escrow service</li>
                                            <li>You can cancel within 24 hours of booking confirmation</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('properties.show', $property) }}"
                               class="text-gray-600 hover:text-gray-800">
                                ← Back to Property
                            </a>
                            <button type="submit"
                                    class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 transition duration-200">
                                Submit Booking Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calculate lease duration and total cost based on dates
        document.addEventListener('DOMContentLoaded', function() {
            const moveInDate = document.getElementById('move_in_date');
            const moveOutDate = document.getElementById('move_out_date');
            const leaseDuration = document.getElementById('lease-duration');
            const totalRent = document.getElementById('total-rent');
            const totalDue = document.getElementById('total-due');
            const monthlyRent = {{ $property->rent_amount }};
            const depositAmount = {{ $property->deposit_amount }};

            function updateCosts() {
                if (moveInDate.value && moveOutDate.value) {
                    const start = new Date(moveInDate.value);
                    const end = new Date(moveOutDate.value);
                    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

                    if (days > 0) {
                        const dailyRate = monthlyRent / 30;
                        const totalRentAmount = dailyRate * days;

                        leaseDuration.textContent = days + ' days';
                        totalRent.textContent = '$' + Math.round(totalRentAmount).toLocaleString();
                        totalDue.textContent = '$' + Math.round(totalRentAmount + depositAmount).toLocaleString();
                    }
                }
            }

            moveInDate.addEventListener('change', updateCosts);
            moveOutDate.addEventListener('change', updateCosts);
        });
    </script>
</x-app>
