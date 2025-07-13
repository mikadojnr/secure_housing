<!DOCTYPE html>
<html>
<head>
    <title>Admin Reviews</title>
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-900 font-montserrat">Reviews</h2>
                <p class="text-gray-600 mt-1">Manage platform reviews</p>
            </div>
            <div class="p-6">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Student</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Property</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $review)
                            <tr class="border-b border-gray-200">
                                <td class="p-3 text-sm text-gray-900">{{ $review->student->name ?? 'N/A' }}</td>
                                <td class="p-3 text-sm text-gray-900">{{ $review->property->title ?? 'N/A' }}</td>
                                <td class="p-3 text-sm text-gray-900">{{ $review->rating }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
