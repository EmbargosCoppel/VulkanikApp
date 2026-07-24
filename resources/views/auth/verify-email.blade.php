<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Before proceeding, please check your email for a verification link.') }}
        {{ __('If you did not receive the email') }},
        <form class="inline" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="underline text-gray-600 hover:text-gray-900">
                {{ __('click here to request another') }}
            </button>.
        </form>
    </div>

    {{ session('status') == 'verification-link-sent'
        ? '<div class="mb-4 font-medium text-sm text-green-600">' . __('A new verification link has been sent to your email address.') . '</div>'
        : '' }}
</x-guest-layout>
