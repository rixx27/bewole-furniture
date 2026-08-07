<x-layouts::auth.premium :title="__('Log in')">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex w-full flex-col text-center animate-fade-in">
            <h1 class="text-[1.75rem] font-semibold tracking-tight text-[#2D2D2D]">{{ __('Welcome Back') }}</h1>
            <p class="mt-1.5 text-sm text-[#2D2D2D]/55">{{ __('Masuk untuk melanjutkan ke dashboard.') }}</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        {{-- Passkey --}}
        <x-passkey-verify class="animate-fade-in" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            {{-- Email Address --}}
            <div class="flex flex-col gap-1.5 animate-fade-in">
                <label for="email" class="text-sm font-medium text-[#2D2D2D]/80">
                    {{ __('Email address') }}
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#A67C52]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </span>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="liquid-glass-input w-full rounded-xl py-3 pl-12 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35"
                    />
                </div>
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1.5 animate-fade-in">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-medium text-[#2D2D2D]/80">
                        {{ __('Password') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="text-sm font-medium text-[#A67C52] transition-colors hover:text-[#5B3A29]">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#A67C52]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </span>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        class="liquid-glass-input w-full rounded-xl py-3 pl-12 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35"
                    />
                </div>
                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <label class="flex cursor-pointer select-none items-center gap-2.5 animate-fade-in">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    {{ old('remember') ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-[#A67C52]/40 text-[#5B3A29] accent-[#5B3A29]"
                />
                <span class="text-sm text-[#2D2D2D]/70">{{ __('Remember me') }}</span>
            </label>

            {{-- Login Button (scale) --}}
            <button
                type="submit"
                data-test="login-button"
                class="mt-1 w-full animate-scale-up rounded-xl bg-[#5B3A29] py-3.5 text-sm font-semibold text-[#EDE0CC] shadow-lg shadow-[#5B3A29]/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#4A2F22] hover:shadow-xl hover:shadow-[#5B3A29]/30 focus:outline-none focus:ring-4 focus:ring-[#A67C52]/30 active:translate-y-0"
            >
                {{ __('Log in') }}
            </button>
        </form>

        {{-- Register --}}
        <div class="mt-1 flex items-center justify-center gap-1.5 text-sm text-[#2D2D2D]/60 animate-fade-in">
            <span>{{ __("Belum punya akun?") }}</span>
            <a href="{{ route('register') }}" wire:navigate class="font-semibold text-[#5B3A29] transition-colors hover:text-[#A67C52]">
                {{ __('Register') }}
            </a>
        </div>
    </div>
</x-layouts::auth.premium>
