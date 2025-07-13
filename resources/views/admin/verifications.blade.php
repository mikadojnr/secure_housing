<!DOCTYPE html>
<html>
<head>
    <title>Admin Verifications</title>
    @livewireStyles
</head>
<body>
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
                            @if ($verification->verification_type === 'identity')
                                <p>Country: {{ $verification->verification_data['country'] }}</p>
                                <p>Document Type: {{ $verification->verification_data['document_type'] }}</p>
                                <p>Document Number: {{ $verification->verification_data['identity_document_number'] }}</p>
                                <a href="{{ Storage::disk('private')->url($verification->verification_data['identity_document_front_path']) }}" target="_blank">View Front</a>
                                @if ($verification->verification_data['identity_document_back_path'])
                                    <a href="{{ Storage::disk('private')->url($verification->verification_data['identity_document_back_path']) }}" target="_blank">View Back</a>
                                @endif
                                <a href="{{ Storage::disk('private')->url($verification->verification_data['selfie_path']) }}" target="_blank">View Selfie</a>
                            @elseif ($verification->verification_type === 'student')
                                <p>University: {{ $verification->verification_data['university'] }}</p>
                                <p>Student ID: {{ $verification->verification_data['student_id'] }}</p>
                                <a href="{{ Storage::disk('private')->url($verification->verification_data['document_path']) }}" target="_blank">View Document</a>
                            @endif
                        </td>
                        <td class="border p-2">
                            @if ($verification->status === 'pending')
                                <form action="{{ route('admin.verifications.approve', $verification) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-green-500 text-white px-2 py-1">Approve</button>
                                </form>
                                <form action="{{ route('admin.verifications.reject', $verification) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="rejection_reason" placeholder="Rejection reason" required>
                                    <button type="submit" class="bg-red-500 text-white px-2 py-1">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @livewireScripts
</body>
</html>
