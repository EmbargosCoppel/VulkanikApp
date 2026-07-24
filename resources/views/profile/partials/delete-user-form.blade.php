<x-danger-button x-data="{ open: false }" x-on:click="open = true">Delete Account</x-danger-button>

<x-modal :show="false" x-model="open">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Are you sure you want to delete your account?</h2>
        <p class="mt-1 text-sm text-gray-600">Once your account is deleted, all of its resources and data will be permanently deleted.</p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="open = false">Cancel</x-secondary-button>

            <form method="POST" action="{{ route('profile.destroy') }}" class="ms-3">
                @csrf
                @method('delete')
                <x-input-error for="password" class="mt-2" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Password" required autocomplete="current-password" x-model="password" />
                <x-danger-button class="mt-3 ms-3">Delete Account</x-danger-button>
            </form>
        </div>
    </div>
</x-modal>
