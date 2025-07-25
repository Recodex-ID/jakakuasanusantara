<x-layouts.auth :title="__('Login')">
    <!-- Login Card -->
    <div
        class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ __('Login') }}</h1>
                <p class="text-gray-600 mt-1">Sign in to your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Username Input -->
                <div class="mb-4">
                    <x-forms.input label="Username" name="username" type="text" placeholder="Enter your username" />
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <x-forms.input label="Password" name="password" type="password" placeholder="••••••••" />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs hover:underline" style="color: #132C5E;">{{ __('Forgot password?') }}</a>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <x-forms.checkbox label="Remember me" name="remember" />
                </div>

                <!-- Login Button -->
                <x-button type="primary" class="w-full">{{ __('Sign In') }}</x-button>
            </form>

            @if (Route::has('register'))
                <!-- Register Link -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        {{ __('Don\'t have an account?') }}
                        <a href="{{ route('register') }}"
                            class="hover:underline font-medium" style="color: #132C5E;">{{ __('Sign up') }}</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.auth>
