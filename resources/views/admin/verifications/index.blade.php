<x-app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Verification Management') }}
            </h2>
            <div class="flex space-x-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    {{ $pendingCount }} Pending
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ $verifiedCount }} Verified
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.verifications') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-0">
                            <select name="type" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">All Types</option>
                                <option value="identity" {{ request('type') === 'identity' ? 'selected' : '' }}>Identity</option>
                                <option value="student" {{ request('type') === 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <select name="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search by user name or email..."
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['type', 'status', 'search']))
                            <a href="{{ route('admin.verifications') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Verifications Table -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Submitted
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Provider
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($verifications as $verification)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full"
                                                     src="{{ $verification->user->profile_photo_url }}"
                                                     alt="{{ $verification->user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $verification->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $verification->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $verification->verification_type === 'identity' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($verification->verification_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($verification->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif($verification->status === 'verified')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Verified
                                            </span>
                                        @elseif($verification->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($verification->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $verification->created_at->format('M j, Y') }}
                                        <div class="text-xs text-gray-400">
                                            {{ $verification->created_at->format('g:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="capitalize">{{ $verification->provider ?? 'Manual' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="openVerificationModal({{ $verification->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Review
                                        </button>
                                        @if($verification->status === 'pending')
                                            <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-900 mr-3"
                                                        onclick="return confirm('Are you sure you want to approve this verification?')">
                                                    Approve
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $verification->id }})"
                                                    class="text-red-600 hover:text-red-900">
                                                Reject
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No verifications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($verifications->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $verifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Verification Review Modals -->
    @foreach($verifications as $verification)
        <div id="verificationModal_{{ $verification->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title_{{ $verification->id }}" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title_{{ $verification->id }}">
                                    Verification Details for {{ $verification->user->name }}
                                </h3>
                                @php
                                    $verificationData = is_array($verification->verification_data) ? $verification->verification_data : [];
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">User Name:</p>
                                        <p class="text-base text-gray-900">{{ $verification->user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">User Email:</p>
                                        <p class="text-base text-gray-900">{{ $verification->user->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Verification Type:</p>
                                        <p class="text-base text-gray-900 capitalize">{{ str_replace('_', ' ', $verification->verification_type) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Status:</p>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($verification->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($verification->status === 'verified') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($verification->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Submitted At:</p>
                                        <p class="text-base text-gray-900">{{ $verification->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    @if($verification->reviewed_at)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Reviewed At:</p>
                                            <p class="text-base text-gray-900">{{ $verification->reviewed_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    @endif
                                    @if($verification->provider)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Provider:</p>
                                            <p class="text-base text-gray-900 capitalize">{{ $verification->provider }}</p>
                                        </div>
                                    @endif
                                    @if($verification->external_id)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">External ID:</p>
                                            <p class="text-base text-gray-900">{{ $verification->external_id }}</p>
                                        </div>
                                    @endif
                                </div>

                                <h5 class="text-lg font-semibold text-gray-900 mb-3">Submitted Data:</h5>
                                <div class="bg-gray-50 p-4 rounded-lg space-y-3 mb-6">
                                    @if($verification->verification_type === 'identity')
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Country:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['country'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Document Type:</span>
                                            <span class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $verificationData['document_type'] ?? 'N/A') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Document Number:</span>
                                            <span class="text-sm text-gray- Developments: </span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Home Town Address:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['home_town_address'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Next of Kin:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['next_of_kin'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                            @if (isset($verificationData['documents']['identity_document_front_path']))
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">ID Front:</p>
                                                    <a href="{{ route('admin.verification-documents.download', ['verification' => $verification->id, 'documentType' => 'identity_document_front']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        View Document
                                                    </a>
                                                    <img src="{{ route('admin.verification-documents.show', $verificationData['documents']['identity_document_front_path']) }}" alt="ID Front" class="mt-2 max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif

                                            @if (isset($verificationData['documents']['identity_document_back_path']))
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">ID Back:</p>
                                                    <a href="{{ route('admin.verification-documents.download', ['verification' => $verification->id, 'documentType' => 'identity_document_back']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        View Document
                                                    </a>
                                                    <img src="{{ route('admin.verification-documents.show', $verificationData['documents']['identity_document_back_path']) }}" alt="ID Back" class="mt-2 max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif
                                            @if (isset($verificationData['documents']['selfie_path']))
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Selfie:</p>
                                                    <a href="{{ route('admin.verification-documents.download', ['verification' => $verification->id, 'documentType' => 'selfie']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        View Document
                                                    </a>
                                                    <img src="{{ route('admin.verification-documents.show', $verificationData['documents']['selfie_path']) }}" alt="Selfie" class="mt-2 max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($verification->verification_type === 'student')
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">University:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['university'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Student ID:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['student_id'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Enrollment Year:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['enrollment_year'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm font-medium text-gray-700">Degree Program:</span>
                                            <span class="text-sm text-gray-900">{{ $verificationData['degree_program'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                            @if (isset($verificationData['documents']['enrollment_document_path']))
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Enrollment Document:</p>
                                                    <a href="{{ route('admin.verification-documents.download', ['verification' => $verification->id, 'documentType' => 'enrollment_document']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        View Document
                                                    </a>
                                                    @php
                                                        $fileExtension = pathinfo($verificationData['documents']['enrollment_document_path'], PATHINFO_EXTENSION);
                                                    @endphp
                                                    @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ route('admin.verification-documents.show', $verificationData['documents']['enrollment_document_path']) }}" alt="Enrollment Document" class="mt-2 max-w-full h-auto rounded-md shadow-sm">
                                                    @else
                                                        <p class="text-sm text-gray-500 mt-2">File type: {{ $fileExtension }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (isset($verificationData['documents']['student_id_card_path']))
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Student ID Card:</p>
                                                    <a href="{{ route('admin.verification-documents.download', ['verification' => $verification->id, 'documentType' => 'student_id_card']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        View Document
                                                    </a>
                                                    <img src="{{ route('admin.verification-documents.show', $verificationData['documents']['student_id_card_path']) }}" alt="Student ID Card" class="mt-2 max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">No specific data submitted for this verification type.</p>
                                    @endif
                                </div>

                                @if($verification->rejection_reason)
                                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <h5 class="text-lg font-semibold text-red-800 mb-2">Rejection Reason:</h5>
                                        <p class="text-base text-red-700">{{ $verification->rejection_reason }}</p>
                                    </div>
                                @endif

                                @if($verification->admin_notes)
                                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <h5 class="text-lg font-semibold text-blue-800 mb-2">Admin Notes:</h5>
                                        <p class="text-base text-blue-700">{{ $verification->admin_notes }}</p>
                                    </div>
                                @endif

                                @if($verification->status === 'pending')
                                    <div class="mt-6">
                                        <h5 class="text-lg font-semibold text-gray-900 mb-3">Actions:</h5>
                                        <div class="flex space-x-3">
                                            <form id="approveForm_{{ $verification->id }}" method="POST" action="{{ route('admin.verifications.approve', $verification) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-4">
                                                    <label for="admin_notes_approve_{{ $verification->id }}" class="block text-sm font-medium text-gray-700">Admin Notes (Optional)</label>
                                                    <textarea name="admin_notes" id="admin_notes_approve_{{ $verification->id }}" rows="3"
                                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                              placeholder="Any additional notes for internal use..."></textarea>
                                                </div>
                                                <button type="submit"
                                                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                                                    Approve Verification
                                                </button>
                                            </form>
                                            <button type="button" onclick="openRejectModal({{ $verification->id }})"
                                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                                                    Reject Verification
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="closeVerificationModal({{ $verification->id }})"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="reject-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="reject-modal-title">
                                    Reject Verification
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Please provide a reason for rejecting this verification. This will be shown to the user.
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                                    <select name="rejection_reason" id="rejection_reason" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select a reason...</option>
                                        <option value="Document quality too poor">Document quality too poor</option>
                                        <option value="Documents do not match">Documents do not match</option>
                                        <option value="Invalid or expired documents">Invalid or expired documents</option>
                                        <option value="Information does not match profile">Information does not match profile</option>
                                        <option value="Suspected fraudulent documents">Suspected fraudulent documents</option>
                                        <option value="Missing required documents">Missing required documents</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700">Additional Notes (Optional)</label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                              placeholder="Any additional notes for internal use..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Reject Verification
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openVerificationModal(verificationId) {
            // Hide all verification modals
            document.querySelectorAll('[id^="verificationModal_"]').forEach(modal => {
                modal.classList.add('hidden');
            });
            // Show the selected verification modal
            const modal = document.getElementById('verificationModal_' + verificationId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeVerificationModal(verificationId) {
            const modal = document.getElementById('verificationModal_' + verificationId);
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function openRejectModal(verificationId) {
            document.getElementById('rejectForm').action = `{{ route('admin.verifications.reject', ':id') }}`.replace(':id', verificationId);
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectForm').reset();
        }

        document.addEventListener('click', function(event) {
            const rejectModal = document.getElementById('rejectModal');
            document.querySelectorAll('[id^="verificationModal_"]').forEach(modal => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
            if (event.target === rejectModal) {
                closeRejectModal();
            }
        });
    </script>
</x-app>
