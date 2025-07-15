@php use Illuminate\Support\Str; @endphp
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
    <div class="sm:flex sm:items-start">
        <div class="w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                Verification Details for {{ $verification->user->name }}
            </h3>
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
                        <span class="text-sm text-gray-900">{{ $verificationData['identity_document_number'] ?? 'N/A' }}</span>
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
