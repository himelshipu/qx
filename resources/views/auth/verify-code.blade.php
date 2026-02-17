<x-guest-layout>
    @if (session('email'))
        <!-- Step 2: Enter Verification Code -->
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Enter the 6-digit verification code sent to ') }} <strong>{{ session('email') }}</strong>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        @if ($errors->any())
            <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('verification.verify') }}">
            @csrf

            <!-- Hidden Email -->
            <input type="hidden" name="email" value="{{ session('email') }}">

            <!-- Verification Code -->
            <div>
                <x-input-label for="code" :value="__('Verification Code')" />
                <x-text-input id="code" class="block mt-1 w-full"
                                type="text"
                                name="code"
                                :value="old('code')"
                                placeholder="000000"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required
                                autofocus
                                inputmode="numeric"
                                autocomplete="off" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    {{ __('Enter the 6-digit code from your email. Valid for 2 minutes.') }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between mt-6">
                <form method="GET" action="{{ route('verification.notice') }}" class="inline">
                    <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        {{ __('Use a different email?') }}
                    </button>
                </form>

                <x-primary-button class="ms-3">
                    {{ __('Verify') }}
                </x-primary-button>
            </div>

            <!-- Resend Code Link -->
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __("Didn't receive the code?") }}
                    <form method="POST" action="{{ route('verification.send') }}" class="inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                        <button type="submit" class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Send again') }}
                        </button>
                    </form>
                </p>
            </div>
        </form>
    @else
        <!-- Step 1: Enter Email Address -->
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Enter your email address to receive a verification code.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        @if ($errors->any())
            <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button>
                    {{ __('Send Verification Code') }}
                </x-primary-button>
            </div>
        </form>
    @endif
</x-guest-layout>
