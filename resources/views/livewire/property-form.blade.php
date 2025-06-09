<div class="bg-white rounded-lg shadow-sm border">
    <div class="px-6 py-4 border-b">
        <h2 class="text-xl font-semibold text-gray-900">
            {{ $property ? 'Edit Property' : 'Create New Property' }}
        </h2>
        <p class="text-gray-600 mt-1">
            {{ $property ? 'Update your property information' : 'Add a new property to your listings' }}
        </p>
    </div>

    <form wire:submit="save" class="p-6 space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Property Title</label>
                <input type="text"
                       id="title"
                       wire:model="title"
                       placeholder="e.g., Spacious 2BR Apartment Near Campus"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description"
                          wire:model="description"
                          rows="4"
                          placeholder="Describe your property, its features, and what makes it special..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="propertyType" class="block text-sm font-medium text-gray-700 mb-1">Property Type</label>
                    <select id="propertyType"
                            wire:model="propertyType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="apartment">Apartment</option>
                        <option value="house">House</option>
                        <option value="room">Room</option>
                        <option value="studio">Studio</option>
                        <option value="shared">Shared Space</option>
                    </select>
                    @error('propertyType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status"
                            wire:model="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Pricing</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="rentAmount" class="block text-sm font-medium text-gray-700 mb-1">Monthly Rent ($)</label>
                    <input type="number"
                           id="rentAmount"
                           wire:model="rentAmount"
                           min="0"
                           step="0.01"
                           placeholder="1500"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('rentAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="depositAmount" class="block text-sm font-medium text-gray-700 mb-1">Security Deposit ($)</label>
                    <input type="number"
                           id="depositAmount"
                           wire:model="depositAmount"
                           min="0"
                           step="0.01"
                           placeholder="1500"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('depositAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Location</h3>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                <input type="text"
                       id="address"
                       wire:model="address"
                       placeholder="123 Main Street"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text"
                           id="city"
                           wire:model="city"
                           placeholder="Boston"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text"
                           id="state"
                           wire:model="state"
                           placeholder="MA"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text"
                           id="postalCode"
                           wire:model="postalCode"
                           placeholder="02101"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('postalCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Property Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-1">Bedrooms</label>
                    <input type="number"
                           id="bedrooms"
                           wire:model="bedrooms"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('bedrooms') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-1">Bathrooms</label>
                    <input type="number"
                           id="bathrooms"
                           wire:model="bathrooms"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('bathrooms') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="maxOccupants" class="block text-sm font-medium text-gray-700 mb-1">Max Occupants</label>
                    <input type="number"
                           id="maxOccupants"
                           wire:model="maxOccupants"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('maxOccupants') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Availability -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Availability</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="availableFrom" class="block text-sm font-medium text-gray-700 mb-1">Available From</label>
                    <input type="date"
                           id="availableFrom"
                           wire:model="availableFrom"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('availableFrom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="availableUntil" class="block text-sm font-medium text-gray-700 mb-1">Available Until (Optional)</label>
                    <input type="date"
                           id="availableUntil"
                           wire:model="availableUntil"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('availableUntil') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Amenities -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Amenities</h3>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($amenityOptions as $key => $label)
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="amenities"
                               value="{{ $key }}"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Utilities Included -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Utilities Included</h3>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($utilityOptions as $key => $label)
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="utilitiesIncluded"
                               value="{{ $key }}"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Images -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Property Images</h3>

            @if($existingImages && $existingImages->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    @foreach($existingImages as $image)
                        <div class="relative">
                            <img src="{{ Storage::url($image->image_path) }}"
                                 alt="Property image"
                                 class="w-full h-24 object-cover rounded-lg">
                            <button type="button"
                                    wire:click="removeExistingImage({{ $image->id }})"
                                    class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-700">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Upload New Images</label>
                <input type="file"
                       id="images"
                       wire:model="images"
                       multiple
                       accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Upload multiple images (JPEG, PNG, GIF - Max 5MB each)</p>
                @error('images.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            @if($images)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($images as $image)
                        <div class="relative">
                            <img src="{{ $image->temporaryUrl() }}"
                                 alt="Preview"
                                 class="w-full h-24 object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('landlord.properties') }}"
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-md font-medium transition-colors">
                <span wire:loading.remove>{{ $property ? 'Update Property' : 'Create Property' }}</span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
        </div>
    </form>
</div>
