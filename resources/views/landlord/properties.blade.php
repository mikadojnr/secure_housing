<x-app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Properties') }}
            </h2>
            <a href="{{ route('properties.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Property
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($properties->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($properties as $property)
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    @if($property->images->count() > 0)
                                        <img src="{{ Storage::url($property->images->first()->image_path) }}"
                                             alt="{{ $property->title }}"
                                             class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">No Image</span>
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <h3 class="font-semibold text-lg mb-2">{{ $property->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($property->description, 100) }}</p>
                                        <p class="text-gray-500 text-sm mb-2">{{ $property->city }}, {{ $property->state }}</p>
                                        <p class="text-green-600 font-semibold mb-2">${{ number_format($property->rent_amount) }}/month</p>

                                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                            <span>{{ $property->bookings_count }} bookings</span>
                                            <span>{{ $property->reviews_count }} reviews</span>
                                            <span class="px-2 py-1 rounded text-xs {{ $property->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($property->status) }}
                                            </span>
                                        </div>

                                        <div class="flex space-x-2">
                                            <a href="{{ route('properties.show', $property) }}"
                                               class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm">
                                                View
                                            </a>
                                            <a href="{{ route('properties.edit', $property) }}"
                                               class="flex-1 bg-yellow-500 hover:bg-yellow-700 text-white text-center py-2 px-3 rounded text-sm">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('properties.destroy', $property) }}"
                                                  class="flex-1" onsubmit="return confirm('Are you sure you want to delete this property?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full bg-red-500 hover:bg-red-700 text-white py-2 px-3 rounded text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $properties->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No properties yet</h3>
                            <p class="text-gray-500 mb-4">Start by creating your first property listing.</p>
                            <a href="{{ route('properties.create') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Your First Property
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app>
