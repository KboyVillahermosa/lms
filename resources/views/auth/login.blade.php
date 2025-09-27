<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="max-w-md mx-auto">
        <header class="text-center mb-6">
            <h1 class="text-3xl font-extrabold text-text-primary">{{ __('Welcome back') }}</h1>
            <p class="mt-3 text-sm text-text-muted">{{ __('Enter your credentials to access your account') }}</p>
        </header>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full bg-white/70 border-gray-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full bg-white/70 border-gray-200"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div>
                <x-primary-button class=" rounded-lg bg-primary hover:bg-primary-700 text-white text-center py-3 text-sm font-semibold">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>

            <div class="text-center text-sm text-gray-500">
                {{ "" }}{{ __('Don\'t have an account?') }}
                <a href="{{ route('register') }}" class="text-accent hover:underline ms-1">{{ __('Register') }}</a>
            </div>
        </form>
    </div>
</x-guest-layout>
