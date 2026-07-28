<form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('patch')

    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $user->email) }}" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />

        @if ($user->hasVerifiedEmail())
            <p class="mt-2 text-sm text-green-600">Your email address has been verified.</p>
        @else
            <p class="mt-2 text-sm text-gray-600">
                Your email address is unverified.
                <form method="POST" action="{{ route('verification.resend') }}" class="mt-2">
                    @csrf
                    <x-primary-button>
                        Re-send verification email
                    </x-primary-button>
                </form>
            </p>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
        <x-action-message class="me-3" on="profile-updated" />
    </div>
</form>
