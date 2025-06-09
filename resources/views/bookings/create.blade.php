<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-50 px-6 py-4 border-b">
                <h1 class="text-2xl font-bold text-gray-900 font-montserrat">Book Property</h1>
                <p class="text-gray-600 mt-1">Complete your booking for {{ $property->title }}</p>
            </div>

            <!-- Verification Gate -->
            @if(!auth()->user()->isVerified('identity'))
                <div class="p-6 bg-yellow-50 border-b">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-yellow-800">Verification Required</h3>
                            <p class="text-yellow-700">Please complete identity verification before booking.</p>
                            <a href="{{ route('verification.center') }}" class="text-yellow-800 underline font-medium">
                                Complete Verification →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-6">
                <!-- Property Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-4">
                        @if($property->primaryImage)
                            <img src="{{ Storage::url($property->primaryImage->image_path) }}"
                                 alt="{{ $property->title }}"
                                 class="w-20 h-20 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $property->title }}</h3>
                            <p class="text-gray-600 text-sm">{{ $property->address }}, {{ $property->city }}</p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-lg font-bold text-blue-600">${{ number_format($property->rent_amount) }}/month</span>
                                <span class="text-sm text-gray-500">{{ $property->bedrooms }}bd • {{ $property->bathrooms }}ba</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="move_in_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Move-in Date
                            </label>
                            <input type="date"
                                   id="move_in_date"
                                   name="move_in_date"
                                   min="{{ $property->available_from->format('Y-m-d') }}"
                                   value="{{ old('move_in_date', $property->available_from->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            @error('move_in_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="move_out_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Move-out Date (Optional)
                            </label>
                            <input type="date"
                                   id="move_out_date"
                                   name="move_out_date"
                                   value="{{ old('move_out_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('move_out_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                            Message to Landlord (Optional)
                        </label>
                        <textarea id="message"
                                  name="message"
                                  rows="4"
                                  placeholder="Introduce yourself and mention any specific requirements..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pricing Breakdown -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Pricing Breakdown</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Monthly Rent</span>
                                <span class="font-medium">${{ number_format($property->rent_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Security Deposit</span>
                                <span class="font-medium">${{ number_format($property->deposit_amount) }}</span>
                            </div>
                            <div class="border-t border-blue-200 pt-2 flex justify-between">
                                <span class="font-semibold text-gray-900">Initial Payment</span>
                                <span class="font-bold text-blue-600">${{ number_format($property->rent_amount + $property->deposit_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fraud Prevention Warning -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-red-800">Security Reminder</h4>
                                <ul class="text-red-700 text-sm mt-1 space-y-1">
                                    <li>• Never pay outside this platform</li>
                                    <li>• Verify physical keys before final payment</li>
                                    <li>• All payments are protected by escrow</li>
                                    <li>• Report suspicious requests immediately</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium transition-colors"
                                @if(!auth()->user()->isVerified('identity')) disabled @endif>
                            Submit Booking Request
                        </button>
                        <a href="{{ route('properties.show', $property) }}"
                           class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
