<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Verification Center</h2>
            <p class="mt-2 text-gray-600">Verify your identity and student status to increase your trust score and access more features.</p>
        </div>

        <!-- Trust Score Display -->
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Trust Score</h3>
                    <p class="text-sm text-gray-600">Your current verification level</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-indigo-600">{{ $user->trust_score }}%</div>
                    <div class="text-sm text-gray-500">
                        @if($user->getVerificationLevel() === 'verified')
                            <span class="text-green-600 font-medium">Fully Verified</span>
                        @elseif($user->getVerificationLevel() === 'partial')
                            <span class="text-yellow-600 font-medium">Partially Verified</span>
                        @else
                            <span class="text-gray-600 font-medium">Unverified</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $user->trust_score }}%"></div>
                </div>
            </div>
        </div>

        <!-- Message Display -->
        @if($message)
            <div class="p-4 border-b border-gray-200">
                <div class="rounded-md p-4 @if($messageType === 'success') bg-green-50 border border-green-200 @elseif($messageType === 'error') bg-red-50 border border-red-200 @else bg-blue-50 border border-blue-200 @endif">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if($messageType === 'success')
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($messageType === 'error')
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm @if($messageType === 'success') text-green-800 @elseif($messageType === 'error') text-red-800 @else text-blue-800 @endif">
                                {{ $message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="setActiveTab('identity')"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'identity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0v2m4-2v2"/>
                        </svg>
                        Identity Verification
                        @if($identityVerification)
                            @if($identityVerification->status === 'verified')
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                            @elseif($identityVerification->status === 'pending')
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($identityVerification->status === 'rejected')
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                            @endif
                        @endif
                    </div>
                </button>

                @if($user->isStudent())
                    <button wire:click="setActiveTab('student')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'student' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Student Verification
                            @if($studentVerification)
                                @if($studentVerification->status === 'verified')
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                @elseif($studentVerification->status === 'pending')
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($studentVerification->status === 'rejected')
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                @endif
                            @endif
                        </div>
                    </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'identity')
                <!-- Identity Verification Tab -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Identity Verification</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Upload your government-issued ID and a selfie to verify your identity. This helps build trust with other users.
                        </p>
                    </div>

                    @if($identityVerification && $identityVerification->status === 'verified')
                        <div class="rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Identity Verified</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Your identity has been successfully verified on {{ $identityVerification->verified_at->format('M j, Y') }}.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($identityVerification && $identityVerification->status === 'pending')
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Verification Pending</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Your identity verification is being reviewed. This usually takes 1-2 business days.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($identityVerification && $identityVerification->status === 'rejected')
                        <div class="rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Verification Rejected</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ $identityVerification->rejection_reason ?? 'Your identity verification was rejected. Please try again with clearer documents.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!$identityVerification || $identityVerification->status === 'rejected')
                        <form wire:submit.prevent="submitIdentityVerification" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-label for="country" value="{{ __('Country of Issuance') }}" />
                                    <select id="country" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model="country" required>
                                        <option value="">Select Country</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="United States">United States</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Australia">Australia</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                    <x-input-error for="country" class="mt-2" />
                                </div>

                                <div>
                                    <x-label for="document_type" value="{{ __('Document Type') }}" />
                                    <select id="document_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model="document_type" required>
                                        <option value="">Select Document Type</option>
                                        <option value="international_passport">International Passport</option>
                                        <option value="drivers_license">Driver's License</option>
                                        <option value="national_identity_number">National Identity Number (NIN)</option>
                                        <option value="voters_card">Voter's Card</option>
                                        <!-- Add more document types as needed -->
                                    </select>
                                    <x-input-error for="document_type" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-label for="identity_document_number" value="{{ __('Identity Document Number') }}" />
                                    <x-input id="identity_document_number" type="text" class="mt-1 block w-full" wire:model="identity_document_number" required />
                                    <x-input-error for="identity_document_number" class="mt-2" />
                                </div>

                                <!-- ID Front -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID Front</label>
                                    <div class="mt-1">
                                        <input type="file" wire:model="identity_document_front" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>
                                    <x-input-error for="identity_document_front" class="mt-2" />
                                    @if($identity_document_front)
                                        <div class="mt-2">
                                            <img src="{{ $identity_document_front->temporaryUrl() }}" class="h-32 w-auto rounded-lg border border-gray-300" alt="ID Front Preview">
                                        </div>
                                    @endif
                                </div>

                                <!-- ID Back -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID Back</label>
                                    <div class="mt-1">
                                        <input type="file" wire:model="identity_document_back" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>
                                    <x-input-error for="identity_document_back" class="mt-2" />
                                    @if($identity_document_back)
                                        <div class="mt-2">
                                            <img src="{{ $identity_document_back->temporaryUrl() }}" class="h-32 w-auto rounded-lg border border-gray-300" alt="ID Back Preview">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Selfie -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Selfie</label>
                                <p class="mt-1 text-sm text-gray-500">Take a clear selfie holding your ID next to your face</p>
                                <div class="mt-1">
                                    <input type="file" wire:model="selfie" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <x-input-error for="selfie" class="mt-2" />
                                @if($selfie)
                                    <div class="mt-2">
                                        <img src="{{ $selfie->temporaryUrl() }}" class="h-32 w-auto rounded-lg border border-gray-300" alt="Selfie Preview">
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        wire:loading.attr="disabled"
                                        wire:target="submitIdentityVerification">
                                    <span wire:loading.remove wire:target="submitIdentityVerification">Submit Identity Verification</span>
                                    <span wire:loading wire:target="submitIdentityVerification">Submitting...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @elseif($activeTab === 'student')
                <!-- Student Verification Tab -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Student Verification</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Verify your student status by providing your university information and enrollment documents.
                        </p>
                    </div>

                    @if($studentVerification && $studentVerification->status === 'verified')
                        <div class="rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Student Status Verified</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Your student status has been successfully verified on {{ $studentVerification->verified_at->format('M j, Y') }}.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($studentVerification && $studentVerification->status === 'pending')
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Verification Pending</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Your student verification is being reviewed. This usually takes 1-2 business days.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($studentVerification && $studentVerification->status === 'rejected')
                        <div class="rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Verification Rejected</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ $studentVerification->rejection_reason ?? 'Your student verification was rejected. Please try again with valid enrollment documents.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!$studentVerification || $studentVerification->status === 'rejected')
                        <form wire:submit.prevent="submitStudentVerification" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- University -->
                                <div>
                                    <label for="university" class="block text-sm font-medium text-gray-700">University</label>
                                    <input type="text" wire:model="university" id="university"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Enter your university name">
                                    <x-input-error for="university" class="mt-2" />
                                </div>

                                <!-- Student ID -->
                                <div>
                                    <label for="studentId" class="block text-sm font-medium text-gray-700">Student ID</label>
                                    <input type="text" wire:model="student_id" id="studentId"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Enter your student ID">
                                    <x-input-error for="student_id" class="mt-2" />
                                </div>

                                <!-- Enrollment Year -->
                                <div>
                                    <label for="enrollment_year" class="block text-sm font-medium text-gray-700">Enrollment Year</label>
                                    <input type="number" wire:model="enrollment_year" id="enrollment_year"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="2024" min="2000" max="2030">
                                    <x-input-error for="enrollment_year" class="mt-2" />
                                </div>

                                <!-- Degree Program -->
                                <div>
                                    <label for="degree_program" class="block text-sm font-medium text-gray-700">Degree Program</label>
                                    <input type="text" wire:model="degree_program" id="degree_program"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="e.g., Computer Science">
                                    <x-input-error for="degree_program" class="mt-2" />
                                </div>
                            </div>

                            <!-- Enrollment Document -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Enrollment Document</label>
                                <p class="mt-1 text-sm text-gray-500">Upload your enrollment letter, transcript, or student portal screenshot</p>
                                <div class="mt-1">
                                    <input type="file" wire:model="enrollment_document" accept=".pdf,.jpg,.jpeg,.png"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <x-input-error for="enrollment_document" class="mt-2" />
                                @if($enrollment_document)
                                    <div class="mt-2">
                                        @if(in_array($enrollment_document->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ $enrollment_document->temporaryUrl() }}" class="h-32 w-auto rounded-lg border border-gray-300" alt="Enrollment Document Preview">
                                        @else
                                            <p class="text-sm text-gray-600">Preview not available for PDF</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Student ID Card (Optional) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Student ID Card (Optional)</label>
                                <p class="mt-1 text-sm text-gray-500">Upload a photo of your student ID card if available</p>
                                <div class="mt-1">
                                    <input type="file" wire:model="student_id_card" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <x-input-error for="student_id_card" class="mt-2" />
                                @if($student_id_card)
                                    <div class="mt-2">
                                        <img src="{{ $student_id_card->temporaryUrl() }}" class="h-32 w-auto rounded-lg border border-gray-300" alt="Student ID Card Preview">
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        wire:loading.attr="disabled"
                                        wire:target="submitStudentVerification">
                                    <span wire:loading.remove wire:target="submitStudentVerification">Submit Student Verification</span>
                                    <span wire:loading wire:target="submitStudentVerification">Submitting...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

