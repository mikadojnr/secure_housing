<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4 font-montserrat">Find Your Perfect Student Housing</h1>
        <p class="text-gray-600">Discover verified, safe, and affordable accommodations near your university</p>
    </div>

    <!-- Enhanced Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="xl:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Property title, address, description..."
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text"
                       wire:model.live.debounce.300ms="city"
                       placeholder="Enter city"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- University -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">University</label>
                <input type="text"
                       wire:model.live.debounce.300ms="university"
                       placeholder="University name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number"
                           wire:model.live.debounce.300ms="minPrice"
                           placeholder="0"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number"
                           wire:model.live.debounce.300ms="maxPrice"
                           placeholder="5000"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Property Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="propertyType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="apartment">Apartment</option>
                    <option value="house">House</option>
                    <option value="room">Room</option>
                    <option value="studio">Studio</option>
                    <option value="shared">Shared</option>
                </select>
            </div>
        </div>

        <!-- Additional Filters Row -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms</label>
                <select wire:model.live="bedrooms"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Any</option>
                    <option value="1">1+</option>
                    <option value="2">2+</option>
                    <option value="3">3+</option>
                    <option value="4">4+</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Distance</label>
                <select wire:model.live="maxDistance"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Any Distance</option>
                    <option value="1">Within 1 mile</option>
                    <option value="3">Within 3 miles</option>
                    <option value="5">Within 5 miles</option>
                    <option value="10">Within 10 miles</option>
                </select>
            </div>

            <div class="flex items-center mt-6">
                <input type="checkbox"
                       wire:model.live="verifiedOnly"
                       id="verified-only"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="verified-only" class="ml-2 text-sm text-gray-700">
                    Verified landlords only
                </label>
            </div>
        </div>

        <!-- Amenities Filter -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
            <div class="flex flex-wrap gap-2">
                @foreach($availableAmenities as $amenity)
                    <label class="inline-flex items-center">
                        <input type="checkbox"
                               wire:model.live="amenities"
                               value="{{ $amenity }}"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $amenity) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Results Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600">{{ $properties->total() }} properties found</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">Sort by:</span>
            <button wire:click="sortBy('rent_amount')"
                    class="text-sm text-blue-600 hover:text-blue-800 {{ $sortBy === 'rent_amount' ? 'font-semibold' : '' }}">
                Price
                @if($sortBy === 'rent_amount')
                    @if($sortDirection === 'asc') ↑ @else ↓ @endif
                @endif
            </button>
            <button wire:click="sortBy('created_at')"
                    class="text-sm text-blue-600 hover:text-blue-800 {{ $sortBy === 'created_at' ? 'font-semibold' : '' }}">
                Newest
                @if($sortBy === 'created_at')
                    @if($sortDirection === 'asc') ↑ @else ↓ @endif
                @endif
            </button>
            <button wire:click="sortBy('trust_score')"
                    class="text-sm text-blue-600 hover:text-blue-800 {{ $sortBy === 'trust_score' ? 'font-semibold' : '' }}">
                Trust Score
                @if($sortBy === 'trust_score')
                    @if($sortDirection === 'asc') ↑ @else ↓ @endif
                @endif
            </button>
        </div>
    </div>

    <!-- Property Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($properties as $property)
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden hover:shadow-md transition-shadow">
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
                        @if($property->landlord->isVerified())
                            <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-semibold flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                Pending Review
                            </span>
                        @endif
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
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ $amenity }}</span>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                <p class="text-gray-500">Try adjusting your search criteria or filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $properties->links() }}
    </div>
</div>
