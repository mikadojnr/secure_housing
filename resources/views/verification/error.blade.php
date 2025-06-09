<x-app>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-sm border text-center p-8">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-4">Verification Failed</h1>
            <p class="text-gray-600 mb-6">
                We were unable to verify your identity. This could be due to document quality issues or other technical problems.
            </p>

            <div class="space-y-3">
                <a href="{{ route('verification.center') }}"
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium transition-colors block">
                    Try Again
                </a>
                <a href="{{ route('dashboard') }}"
                   class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-6 rounded-md font-medium transition-colors block">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app>
