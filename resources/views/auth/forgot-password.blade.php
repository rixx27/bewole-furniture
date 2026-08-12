<x-layouts::auth.premium :title="__('Forgot password')">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex w-full flex-col text-center animate-fade-in">
            <h1 class="text-[1.75rem] font-semibold tracking-tight text-[#2D2D2D]">{{ __('Lupa Kata Sandi') }}</h1>
            <p class="mt-1.5 text-sm text-[#2D2D2D]/55">{{ __('Masukkan alamat email Anda untuk menerima tautan reset kata sandi.') }}</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

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
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="liquid-glass-input w-full rounded-xl py-3 pl-12 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35"
                    />
                </div>
                @error('email')
                    <p class="text-sm text-[#B91C1C]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button
                type="submit"
                data-test="email-password-reset-link-button"
                class="mt-1 w-full animate-scale-up rounded-xl bg-[#5B3A29] py-3.5 text-sm font-semibold text-[#EDE0CC] shadow-lg shadow-[#5B3A29]/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#4A2F22] hover:shadow-xl hover:shadow-[#5B3A29]/30 focus:outline-none focus:ring-4 focus:ring-[#A67C52]/30 active:translate-y-0"
            >
                {{ __('Kirim Tautan Reset') }}
            </button>
        </form>

        {{-- Return to Login Link --}}
        <div class="mt-1 flex items-center justify-center gap-1.5 text-sm text-[#2D2D2D]/60 animate-fade-in">
            <span>{{ __('Kembali ke halaman') }}</span>
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-[#5B3A29] transition-colors hover:text-[#A67C52]">
                {{ __('Masuk') }}
            </a>
        </div>
    </div>
</x-layouts::auth.premium>
