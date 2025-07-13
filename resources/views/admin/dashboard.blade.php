<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-900 font-montserrat">Admin Dashboard</h2>
                <p class="text-gray-600 mt-1">Overview of platform activity</p>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900">Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Total Students</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Total Landlords</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_landlords'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Pending Verifications</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['pending_verifications'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
