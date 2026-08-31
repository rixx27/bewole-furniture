<x-layouts::auth.premium :title="__('Register')">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex w-full flex-col text-center animate-fade-in">
            <h1 class="text-[1.75rem] font-semibold tracking-tight text-[#2D2D2D]">{{ __('Buat Akun Baru') }}</h1>
            <p class="mt-1.5 text-sm text-[#2D2D2D]/55">{{ __('Lengkapi data di bawah ini untuk membuat akun Anda.') }}</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
            @csrf

            {{-- Full Name --}}
            <div class="flex flex-col gap-1.5 animate-fade-in">
                <label for="name" class="text-sm font-medium text-[#2D2D2D]/80">
                    {{ __('Nama Lengkap') }}
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#A67C52]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </span>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Nama Lengkap"
                        class="liquid-glass-input w-full rounded-xl py-3 pl-12 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35"
                    />
                </div>
                @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email Address --}}
            <div class="flex flex-col gap-1.5 animate-fade-in">
                <label for="email" class="text-sm font-medium text-[#2D2D2D]/80">
                    {{ __('Alamat Email') }}
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
                <label for="password" class="text-sm font-medium text-[#2D2D2D]/80">
                    {{ __('Kata Sandi') }}
                </label>
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
                        autocomplete="new-password"
                        placeholder="Kata Sandi"
                        class="liquid-glass-input w-full rounded-xl py-3 pl-12 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35"
                    />
                </div>
                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="flex flex-col gap-1.5 animate-fade-in">
                <label for="password_confirmation" class="text-sm font-medium text-[#2D2D2D]/80">
                    {{ __('Konfirmasi Kata Sandi') }}
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#A67C52]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.714Z" />
                        </svg>
                    </span>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi Kata Sandi"
                        class="liquid-glass-input w-full rounded-xl py-3 pl-12 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35"
                    />
                </div>
                @error('password_confirmation')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Register Button --}}
            <button
                type="submit"
                data-test="register-user-button"
                class="mt-2 w-full animate-scale-up rounded-xl bg-[#5B3A29] py-3.5 text-sm font-semibold text-[#EDE0CC] shadow-lg shadow-[#5B3A29]/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#4A2F22] hover:shadow-xl hover:shadow-[#5B3A29]/30 focus:outline-none focus:ring-4 focus:ring-[#A67C52]/30 active:translate-y-0"
            >
                {{ __('Daftar Akun') }}
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative flex items-center justify-center animate-fade-in">
            <div class="w-full border-t border-[#5B3A29]/15"></div>
            <span class="relative bg-transparent px-3 text-xs font-medium uppercase tracking-wider text-[#2D2D2D]/50">
                {{ __('atau') }}
            </span>
            <div class="w-full border-t border-[#5B3A29]/15"></div>
        </div>

        {{-- Google Sign Up Button --}}
        <a
            href="{{ route('auth.google') }}"
            class="flex w-full items-center justify-center gap-3 rounded-xl border border-[#5B3A29]/15 bg-white/70 py-3 text-sm font-medium text-[#2D2D2D] shadow-sm backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white hover:border-[#A67C52]/40 hover:shadow-md active:translate-y-0 animate-fade-in"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>{{ __('Daftar dengan Google') }}</span>
        </a>

        {{-- Login Link --}}
        <div class="mt-1 flex items-center justify-center gap-1.5 text-sm text-[#2D2D2D]/60 animate-fade-in">
            <span>{{ __('Sudah memiliki akun?') }}</span>
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-[#5B3A29] transition-colors hover:text-[#A67C52]">
                {{ __('Masuk') }}
            </a>
        </div>
    </div>
</x-layouts::auth.premium>
