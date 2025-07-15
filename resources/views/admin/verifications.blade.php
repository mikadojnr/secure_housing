
<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verification Management') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Verification Requests</h1>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">User</th>
                    <th class="border p-2">Type</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Provider</th>
                    <th class="border p-2">Details</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($verifications as $verification)
                    <tr>
                        <td class="border p-2">{{ $verification->user->name }}</td>
                        <td class="border p-2">{{ $verification->verification_type }}</td>
                        <td class="border p-2">{{ $verification->status }}</td>
                        <td class="border p-2">{{ $verification->provider ?? 'Manual' }}</td>
                        <td class="border p-2">
                            @if ($verification->verification_type === 'identity' && isset($verification->verification_data['documents']))
                                @if (isset($verification->verification_data['country']))
                                    <p>Country: {{ $verification->verification_data['country'] }}</p>
                                @endif
                                @if (isset($verification->verification_data['document_type']))
                                    <p>Document Type: {{ $verification->verification_data['document_type'] }}</p>
                                @endif
                                @if (isset($verification->verification_data['identity_document_number']))
                                    <p>Document Number: {{ $verification->verification_data['identity_document_number'] }}</p>
                                @endif
                                @if (isset($verification->verification_data['documents']['identity_document_front_path']))
                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['documents']['identity_document_front_path']) }}" target="_blank">View Front</a>
                                @endif
                                @if (isset($verification->verification_data['documents']['identity_document_back_path']))
                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['documents']['identity_document_back_path']) }}" target="_blank">View Back</a>
                                @endif
                                @if (isset($verification->verification_data['documents']['selfie_path']))
                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['documents']['selfie_path']) }}" target="_blank">View Selfie</a>
                                @endif
                            @elseif ($verification->verification_type === 'student' && isset($verification->verification_data['documents']))
                                @if (empty($verification->verification_data['university']))
                                    <p>University: <span class="muted">NULL</span></p>
                                @else
                                    <p>University: {{ $verification->verification_data['university'] }}</p>
                                @endif
                                @if (isset($verification->verification_data['student_id']))
                                    <p>Student ID: {{ $verification->verification_data['student_id'] }}</p>
                                @else
                                    <p>Student ID: <span class="muted">NULL</span></p>
                                @endif
                                @if (isset($verification->verification_data['enrollment_year']))
                                    <p>Enrollment Year: {{ $verification->verification_data['enrollment_year'] }}</p>
                                @endif
                                @if (isset($verification->verification_data['degree_program']))
                                    <p>Degree Program: {{ $verification->verification_data['degree_program'] }}</p>
                                @endif
                                @if (isset($verification->verification_data['documents']['enrollment_document_path']))
                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['documents']['enrollment_document_path']) }}" target="_blank">View Enrollment Document</a>
                                @endif
                                @if (isset($verification->verification_data['documents']['student_id_card_path']))
                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['documents']['student_id_card_path']) }}" target="_blank">View Student ID Card</a>
                                @endif
                            @else
                                <p>No details available</p>
                            @endif
                        </td>
                        <td class="border p-2">
                            @if ($verification->status === 'pending')
                                <form action="{{ route('admin.verifications.approve', $verification) }}" method="POST" class="inline-block mr-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Approve</button>
                                </form>
                                <form action="{{ route('admin.verifications.reject', $verification) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="rejection_reason" placeholder="Rejection reason" required class="border p-1 rounded text-sm">
                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded mt-1">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $verifications->links() }}
        </div>
    </div>
</x-app>
