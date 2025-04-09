<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('忘記密碼了嗎？沒問題。只需提供您的電子郵件地址，我們將發送一個密碼重置連結給您，讓您可以設定新的密碼。') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('電子郵件')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('發送密碼重置連結') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
