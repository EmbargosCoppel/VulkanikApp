<form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-6">
    @csrf

    <div>
        <x-input-label for="current_password" value="Current Password" />
        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required autocomplete="current-password" />
        <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
    </div>

    <div>
        <x-input-label for="password" value="New Password" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div>
        <x-input-label for="password_confirmation" value="Confirm Password" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
        <x-action-message class="me-3" on="password-updated" />
    </div>
</form>
