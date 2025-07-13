<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Property Management') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-900 font-montserrat">Properties</h2>
                <p class="text-gray-600 mt-1">Manage platform properties</p>
            </div>
            <div class="p-6">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Title</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Landlord</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($properties as $property)
                            <tr class="border-b border-gray-200">
                                <td class="p-3 text-sm text-gray-900">{{ $property->title }}</td>
                                <td class="p-3 text-sm text-gray-900">{{ $property->landlord->name ?? 'N/A' }}</td>
                                <td class="p-3 text-sm text-gray-900">{{ $property->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app>
