<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>

                <x-profile.update-profile-information-form :user="$user" />
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
                <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>

                <x-profile.update-password-form />
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">Delete Account</h2>
                <p class="mt-1 text-sm text-gray-600">Once your account is deleted, all of its resources and data will be permanently deleted.</p>

                <x-profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
