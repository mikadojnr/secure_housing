<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($properties as $property)
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden hover:shadow-md transition-shadow property-card">
            <!-- Property Image -->
            <div class="relative h-48 bg-gray-200">
                @if($property->primaryImage)
                    <img src="{{ Storage::url($property->primaryImage->image_path) }}"
                         alt="{{ $property->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif

                <!-- Verification Badge -->
                <div class="absolute top-3 left-3">
                    <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-semibold flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Verified
                    </span>
                </div>

                <!-- Trust Score -->
                <div class="absolute top-3 right-3">
                    <div class="bg-white bg-opacity-90 px-2 py-1 rounded-full text-xs font-semibold">
                        ⭐ {{ number_format($property->trust_score, 1) }}
                    </div>
                </div>
            </div>

            <!-- Property Details -->
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $property->title }}</h3>
                    <button class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

                <p class="text-gray-600 text-sm mb-3">{{ $property->address }}, {{ $property->city }}</p>

                <div class="flex items-center justify-between mb-3">
                    <div class="text-2xl font-bold text-blue-600">
                        ${{ number_format($property->rent_amount) }}
                        <span class="text-sm text-gray-500 font-normal">/month</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $property->bedrooms }}bd • {{ $property->bathrooms }}ba
                    </div>
                </div>

                <!-- Property Features -->
                <div class="flex flex-wrap gap-1 mb-3">
                    @if($property->amenities)
                        @foreach(array_slice($property->amenities, 0, 3) as $amenity)
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $amenity)) }}</span>
                        @endforeach
                        @if(count($property->amenities) > 3)
                            <span class="text-gray-500 text-xs">+{{ count($property->amenities) - 3 }} more</span>
                        @endif
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <a href="{{ route('properties.show', $property) }}"
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium transition-colors">
                        View Details
                    </a>
                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-md text-sm font-medium transition-colors">
                        Contact
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No featured properties</h3>
            <p class="text-gray-500">Check back soon for verified listings from trusted landlords.</p>
        </div>
    @endforelse
</div>
