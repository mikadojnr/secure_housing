

<x-app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-900 font-montserrat">Users</h2>
                <p class="text-gray-600 mt-1">Manage platform users</p>
            </div>
            <div class="p-6">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Name</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">User Type</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="border-b border-gray-200 p-3 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b border-gray-200">
                                <td class="p-3 text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="p-3 text-sm text-gray-900">{{ $user->profile->user_type ?? 'N/A' }}</td>
                                <td class="p-3 text-sm text-gray-900">{{ $user->status }}</td>
                                <td class="p-3">
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm font-medium">
                                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app>
