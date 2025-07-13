<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verification Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pending Verifications</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($verifications as $verification)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $verification->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($verification->verification_type) }}
                                        </td>
                                        <td class="p-3 text-sm text-gray-900">
                                            @if($verification->verification_type === 'identity')
                                                <p>Country: {{ data_get($verification->verification_data, 'country', 'N/A') }}</p>
                                                <p>Document Type: {{ data_get($verification->verification_data, 'document_type', 'N/A') }}</p>
                                                <p>Document Number: {{ data_get($verification->verification_data, 'identity_document_number', 'N/A') }}</p>
                                                @if(data_get($verification->verification_data, 'identity_document_front_path'))
                                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['identity_document_front_path']) }}" target="_blank" class="text-blue-600 hover:underline">View Front</a>
                                                @else
                                                    <span class="text-gray-500">No Front Document</span>
                                                @endif
                                                @if(data_get($verification->verification_data, 'identity_document_back_path'))
                                                    <br><a href="{{ Storage::disk('private')->url($verification->verification_data['identity_document_back_path']) }}" target="_blank" class="text-blue-600 hover:underline">View Back</a>
                                                @else
                                                    <br><span class="text-gray-500">No Back Document</span>
                                                @endif
                                                @if(data_get($verification->verification_data, 'selfie_path'))
                                                    <br><a href="{{ Storage::disk('private')->url($verification->verification_data['selfie_path']) }}" target="_blank" class="text-blue-600 hover:underline">View Selfie</a>
                                                @else
                                                    <br><span class="text-gray-500">No Selfie</span>
                                                @endif
                                            @elseif($verification->verification_type === 'student')
                                                <p>University: {{ data_get($verification->verification_data, 'university', 'N/A') }}</p>
                                                <p>Student ID: {{ data_get($verification->verification_data, 'student_id', 'N/A') }}</p>
                                                @if(data_get($verification->verification_data, 'document_path'))
                                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['document_path']) }}" target="_blank" class="text-blue-600 hover:underline">View Document</a>
                                                @else
                                                    <span class="text-gray-500">No Document</span>
                                                @endif
                                            @endif
                                            @if($verification->rejection_reason)
                                                <p class="text-red-600 mt-2">Rejection Reason: {{ $verification->rejection_reason }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $verification->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($verification->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($verification->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($verification->status === 'pending')
                                                <form action="{{ route('admin.verifications.approve', $verification) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.verifications.reject', $verification) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No verifications found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $verifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
