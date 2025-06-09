<x-app>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-sm border text-center p-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-4">Verification Successful!</h1>
            <p class="text-gray-600 mb-6">
                Your identity verification has been completed successfully. You can now access all platform features.
            </p>

            <div class="space-y-3">
                <a href="{{ route('dashboard') }}"
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium transition-colors block">
                    Go to Dashboard
                </a>
                <a href="{{ route('properties.index') }}"
                   class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-6 rounded-md font-medium transition-colors block">
                    Browse Properties
                </a>
            </div>
        </div>
    </div>
</x-app>
