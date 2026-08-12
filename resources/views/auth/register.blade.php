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

        {{-- Login Link --}}
        <div class="mt-1 flex items-center justify-center gap-1.5 text-sm text-[#2D2D2D]/60 animate-fade-in">
            <span>{{ __('Sudah memiliki akun?') }}</span>
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-[#5B3A29] transition-colors hover:text-[#A67C52]">
                {{ __('Masuk') }}
            </a>
        </div>
    </div>
</x-layouts::auth.premium>
