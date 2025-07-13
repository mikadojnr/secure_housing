<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Property Header -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden mb-8">
            <!-- Image Gallery -->
            <div class="relative h-96 bg-gray-200">
                @if($property->images->count() > 0)
                    <div class="swiper-container h-full">
                        <div class="swiper-wrapper">
                            @foreach($property->images as $image)
                                <div class="swiper-slide">
                                    <img src="{{ Storage::url($image->image_path) }}"
                                         alt="{{ $image->alt_text ?? $property->title }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif

                <!-- Verification Badge -->
                <div class="absolute top-4 left-4">
                    @if($property->landlord->isVerified())
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Verified Landlord
                        </span>
                    @else
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Pending Verification
                        </span>
                    @endif
                </div>

                <!-- Trust Score -->
                <div class="absolute top-4 right-4">
                    <div class="bg-white bg-opacity-90 px-3 py-1 rounded-full text-sm font-semibold">
                        ⭐ {{ number_format($property->trust_score, 1) }} Trust Score
                    </div>
                </div>
            </div>

            <!-- Property Details -->
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2 font-montserrat">{{ $property->title }}</h1>
                        <p class="text-gray-600 flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $property->address }}, {{ $property->city }}, {{ $property->state }}
                        </p>
                    </div>
                    <button class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Price and Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-3xl font-bold text-blue-600 mb-1">
                            ${{ number_format($property->rent_amount) }}
                        </div>
                        <div class="text-sm text-gray-600">per month</div>
                        <div class="text-sm text-gray-500 mt-1">
                            Deposit: ${{ number_format($property->deposit_amount) }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-gray-900 mb-1">
                            {{ $property->bedrooms }}bd • {{ $property->bathrooms }}ba
                        </div>
                        <div class="text-sm text-gray-600">
                            Max {{ $property->max_occupants }} occupants
                        </div>
                        <div class="text-sm text-gray-500 mt-1 capitalize">
                            {{ str_replace('_', ' ', $property->property_type) }}
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-lg font-bold text-green-600 mb-1">
                            Available
                        </div>
                        <div class="text-sm text-gray-600">
                            From {{ $property->available_from->format('M j, Y') }}
                        </div>
                        @if($property->available_until)
                            <div class="text-sm text-gray-500 mt-1">
                                Until {{ $property->available_until->format('M j, Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Description -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                <!-- Amenities -->
                @if($property->amenities && count($property->amenities) > 0)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Amenities</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($property->amenities as $amenity)
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Utilities Included -->
                @if($property->utilities_included && count($property->utilities_included) > 0)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Utilities Included</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($property->utilities_included as $utility)
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $utility)) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- House Rules -->
                @if($property->house_rules && count($property->house_rules) > 0)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">House Rules</h2>
                        <ul class="space-y-2">
                            @foreach($property->house_rules as $rule)
                                <li class="flex items-start text-gray-700">
                                    <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $rule }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Reviews -->
                @if($property->reviews->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            Reviews ({{ $property->reviews->count() }})
                        </h2>
                        <div class="space-y-4">
                            @foreach($property->reviews->take(5) as $review)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                {{ substr($review->reviewer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $review->reviewer->name }}</div>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Landlord Info -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Landlord Information</h3>
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            {{ substr($property->landlord->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $property->landlord->name }}</div>
                            <div class="text-sm text-gray-600">
                                Verification: {{ ucfirst($property->landlord->getVerificationLevel()) }}
                            </div>
                        </div>
                    </div>

                    @if($property->landlord->profile && $property->landlord->profile->bio)
                        <p class="text-gray-700 text-sm mb-4">{{ $property->landlord->profile->bio }}</p>
                    @endif

                    <div class="space-y-3">

                        @if (auth()->user()->id != $property->landlord_id)
                            <a href="{{ route('messages.show', ['user' => $property->landlord, 'property_id' => $property->id]) }}"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-center font-medium transition-colors block">
                                Send Message
                            </a>
                        @endif



                        @auth
                            @if(auth()->user()->profile && auth()->user()->profile->user_type === 'student')
                                <a href="{{ route('properties.book', $property) }}"
                                   class="w-full bg-coral-500 hover:bg-coral-600 text-white py-2 px-4 rounded-md text-center font-medium transition-colors block">
                                    Book Now
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full bg-coral-500 hover:bg-coral-600 text-white py-2 px-4 rounded-md text-center font-medium transition-colors block">
                                Login to Book
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Security Information -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Information</h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Landlord Verified</span>
                            @if($property->landlord->isVerified())
                                <span class="text-green-600 font-medium">✓ Yes</span>
                            @else
                                <span class="text-red-600 font-medium">✗ No</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Property Verified</span>
                            @if($property->is_verified)
                                <span class="text-green-600 font-medium">✓ Yes</span>
                            @else
                                <span class="text-yellow-600 font-medium">⏳ Pending</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Trust Score</span>
                            <span class="font-medium">{{ number_format($property->trust_score, 1) }}/5.0</span>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <strong>Safety Tip:</strong> Never send money outside of our secure platform. All payments are protected by escrow.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Properties -->
                @if($similarProperties->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Similar Properties</h3>
                        <div class="space-y-4">
                            @foreach($similarProperties as $similar)
                                <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                    <a href="{{ route('properties.show', $similar) }}" class="block">
                                        <div class="font-medium text-gray-900 mb-1">{{ $similar->title }}</div>
                                        <div class="text-sm text-gray-600 mb-2">{{ $similar->city }}</div>
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-blue-600">${{ number_format($similar->rent_amount) }}/mo</span>
                                            <span class="text-xs text-gray-500">{{ $similar->bedrooms }}bd • {{ $similar->bathrooms }}ba</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
    @endpush

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    @endpush
</x-app>
