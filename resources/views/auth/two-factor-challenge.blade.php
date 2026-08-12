<x-layouts::auth.premium :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput" class="flex w-full flex-col text-center animate-fade-in mb-4">
                <h1 class="text-[1.75rem] font-semibold tracking-tight text-[#2D2D2D]">{{ __('Kode Autentikasi') }}</h1>
                <p class="mt-1.5 text-sm text-[#2D2D2D]/55">{{ __('Masukkan kode autentikasi dari aplikasi authenticator Anda.') }}</p>
            </div>

            <div x-show="showRecoveryInput" class="flex w-full flex-col text-center animate-fade-in mb-4">
                <h1 class="text-[1.75rem] font-semibold tracking-tight text-[#2D2D2D]">{{ __('Kode Pemulihan') }}</h1>
                <p class="mt-1.5 text-sm text-[#2D2D2D]/55">{{ __('Konfirmasi akses dengan memasukkan salah satu kode pemulihan darurat Anda.') }}</p>
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="flex flex-col gap-5">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5" x-ref="otp">
                            <flux:otp
                                x-model="code"
                                length="6"
                                name="code"
                                label="OTP Code"
                                label:sr-only
                                class="mx-auto"
                             />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput" class="animate-fade-in">
                        <div class="my-5 relative">
                            <input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                placeholder="Kode Pemulihan"
                                class="liquid-glass-input w-full rounded-xl py-3 px-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/35 text-center tracking-wider font-mono"
                            />
                        </div>

                        @error('recovery_code')
                            <p class="text-sm text-[#B91C1C]">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full animate-scale-up rounded-xl bg-[#5B3A29] py-3.5 text-sm font-semibold text-[#EDE0CC] shadow-lg shadow-[#5B3A29]/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#4A2F22] hover:shadow-xl hover:shadow-[#5B3A29]/30 focus:outline-none focus:ring-4 focus:ring-[#A67C52]/30 active:translate-y-0"
                    >
                        {{ __('Lanjutkan') }}
                    </button>
                </div>

                <div class="mt-4 space-x-0.5 text-sm text-center text-[#2D2D2D]/60 animate-fade-in">
                    <span>{{ __('atau Anda dapat') }}</span>
                    <div class="inline font-semibold text-[#5B3A29] cursor-pointer hover:text-[#A67C52] transition-colors">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('masuk menggunakan kode pemulihan') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('masuk menggunakan kode autentikasi') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth.premium>
