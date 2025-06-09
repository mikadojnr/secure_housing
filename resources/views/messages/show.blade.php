<x-app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @livewire('message-thread', ['recipient' => $user, 'propertyId' => $propertyId])
    </div>
</x-app>
