<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('感謝您的註冊！在開始之前，請點擊我們剛剛發送給您的電子郵件中的連結來驗證您的電子郵件地址。如果您沒有收到郵件，我們很樂意重新發送一封。') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('新的驗證連結已發送到您註冊時提供的電子郵件地址。') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('重新發送驗證郵件') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('登出') }}
            </button>
        </form>
    </div>
</x-guest-layout>
