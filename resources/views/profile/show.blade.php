<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <!-- Profile Picture -->
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                                    @if(Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_path)
                                        <img class="w-24 h-24 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    @else
                                        <span class="text-white text-2xl font-bold">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                <div class="mt-2">
                                    @if(Auth::user()->user_type === 'landlord')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                            </svg>
                                            Landlord
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Student
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Verification Status -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Verification Status</h4>
                                @if(Auth::user()->isVerified())
                                    <div class="flex items-center text-green-600">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Verified Account</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-yellow-600">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Pending Verification</span>
                                    </div>
                                    <a href="{{ route('verification.center') }}" class="mt-2 text-xs text-blue-600 hover:text-blue-800">
                                        Complete verification →
                                    </a>
                                @endif
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                @if(Auth::user()->user_type === 'landlord')
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->properties()->count() }}</div>
                                        <div class="text-xs text-gray-500">Properties</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ Auth::user()->properties()->where('status', 'rented')->count() }}</div>
                                        <div class="text-xs text-gray-500">Rented</div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->bookings()->count() }}</div>
                                        <div class="text-xs text-gray-500">Bookings</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ Auth::user()->reviews()->count() }}</div>
                                        <div class="text-xs text-gray-500">Reviews</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                        @livewire('profile.update-profile-information-form')
                    @endif

                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                        @livewire('profile.update-password-form')
                    @endif

                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        @livewire('profile.two-factor-authentication-form')
                    @endif

                    @livewire('profile.logout-other-browser-sessions-form')

                    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                        @livewire('profile.delete-user-form')
                    @endif

                    <!-- Custom Profile Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>

                            <form method="POST" action="{{ route('profile.update-additional') }}">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                        <input type="tel" name="phone" id="phone" value="{{ Auth::user()->profile->phone ?? '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ Auth::user()->profile->date_of_birth ?? '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    @if(Auth::user()->user_type === 'student')
                                        <div>
                                            <label for="university" class="block text-sm font-medium text-gray-700">University</label>
                                            <input type="text" name="university" id="university" value="{{ Auth::user()->profile->university ?? '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                                            <input type="text" name="student_id" id="student_id" value="{{ Auth::user()->profile->student_id ?? '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    @endif

                                    <div class="md:col-span-2">
                                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                                        <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ Auth::user()->profile->bio ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Update Additional Information
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
