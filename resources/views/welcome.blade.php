<x-app>
    <div class="relative overflow-hidden">
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 font-montserrat">
                        Secure Student Housing
                        <span class="text-teal-300">Without Scams</span>
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                        Find verified, safe, and affordable student accommodation with our fraud-protected platform.
                        Every landlord is verified, every listing is authentic.
                    </p>

                    <!-- Search Bar -->
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white rounded-lg shadow-xl p-6">
                            <form class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1">
                                    <input type="text"
                                           placeholder="Enter university or city..."
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex gap-2">
                                    <select class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option>Any Price</option>
                                        <option>$500-$1000</option>
                                        <option>$1000-$1500</option>
                                        <option>$1500+</option>
                                    </select>
                                    <button type="submit"
                                            class="bg-coral-500 hover:bg-coral-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Value Proposition -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4 font-montserrat">Why Choose SecureHousing?</h2>
                    <p class="text-lg text-gray-600">Your safety and security are our top priorities</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Verified Listings -->
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Verified Listings</h3>
                        <p class="text-gray-600">Every property and landlord undergoes rigorous identity verification using biometric technology.</p>
                    </div>

                    <!-- Direct Chat -->
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Secure Communication</h3>
                        <p class="text-gray-600">Encrypted messaging with automatic scam detection and fraud prevention alerts.</p>
                    </div>

                    <!-- Fraud Protection -->
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-coral-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-coral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Escrow Protection</h3>
                        <p class="text-gray-600">Secure payment processing with escrow services to protect your deposits and rent payments.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Properties -->
        <div class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4 font-montserrat">Featured Properties</h2>
                    <p class="text-lg text-gray-600">Verified listings from trusted landlords</p>
                </div>

                @livewire('featured-properties')
            </div>
        </div>


    </div>
</x-app>
