<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Kelola Profil')] #[Layout('frontend.layouts.app')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    // Profile fields
    public string $name = '';
    public string $email = '';
    public ?string $phone = '';
    public ?string $avatar = null;
    public $newAvatar = null;

    // Password change fields
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Account information fields
    public ?string $google_id = null;
    public ?string $memberSince = null;
    public string $loginMethod = 'Email & Password';
    public bool $hasPassword = true;

    // Feedback states
    public ?string $profileSuccessMessage = null;
    public ?string $passwordSuccessMessage = null;

    /**
     * Mount component data for authenticated user.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
        $this->avatar = $user->avatar;
        $this->google_id = $user->google_id;
        $this->hasPassword = ! empty($user->password);
        $this->loginMethod = ! empty($user->google_id) ? 'Google' : 'Email & Password';
        $this->memberSince = $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-';
    }

    /**
     * Live validation when avatar is uploaded.
     */
    public function updatedNewAvatar(): void
    {
        $this->validate([
            'newAvatar' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'newAvatar.image' => 'File avatar harus berupa gambar.',
            'newAvatar.mimes' => 'Format file avatar harus jpeg, png, jpg, atau webp.',
            'newAvatar.max' => 'Ukuran file avatar maksimal 2MB.',
        ]);
    }

    /**
     * Delete user avatar and restore default avatar representation.
     */
    public function deleteAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar) {
            if (! Str::startsWith($user->avatar, ['http://', 'https://']) && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->update(['avatar' => null]);
            $this->avatar = null;
            $this->newAvatar = null;

            Log::info("User [ID: {$user->id}] menghapus foto profil.");

            $this->profileSuccessMessage = 'Foto profil berhasil dihapus.';
            $this->passwordSuccessMessage = null;
        }
    }

    /**
     * Update profile information for the authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->emailRules($user->id),
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-()]*$/'],
            'newAvatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'phone.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid.',
            'newAvatar.image' => 'File avatar harus berupa gambar.',
            'newAvatar.mimes' => 'Format file avatar harus jpeg, png, jpg, atau webp.',
            'newAvatar.max' => 'Ukuran file avatar maksimal 2MB.',
        ];

        $validated = $this->validate($rules, $messages);

        $avatarPath = $user->avatar;

        if ($this->newAvatar) {
            // Delete old uploaded local avatar
            if ($user->avatar && ! Str::startsWith($user->avatar, ['http://', 'https://']) && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $extension = $this->newAvatar->getClientOriginalExtension();
            $filename = 'avatar_' . $user->id . '_' . Str::random(16) . '.' . $extension;
            $avatarPath = $this->newAvatar->storeAs('avatars', $filename, 'public');
            $this->newAvatar = null;
            $this->avatar = $avatarPath;
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->avatar = $avatarPath;

        if ($this->email !== $user->email) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }

        $user->save();

        Log::info("User [ID: {$user->id}] memperbarui profil.");

        $this->profileSuccessMessage = 'Profil berhasil diperbarui.';
        $this->passwordSuccessMessage = null;
        $this->dispatch('profile-updated');
    }

    /**
     * Update account password.
     */
    public function updatePassword(): void
    {
        $user = Auth::user();

        // 1. Verifikasi Password Saat Ini (Wajib jika akun memiliki password lokal)
        if (! empty($user->password)) {
            if (empty($this->current_password)) {
                $this->addError('current_password', 'Kata sandi saat ini wajib diisi.');
                return;
            }

            if (! Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Kata sandi saat ini tidak sesuai.');
                return;
            }
        }

        // 2. Validasi Password Baru & Konfirmasi
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.string' => 'Kata sandi baru harus berupa string.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // 3. Password Baru Tidak Boleh Sama dengan Password Saat Ini
        if (! empty($user->password) && Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
            return;
        }

        // 4. Update Password (Hashed)
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->hasPassword = true;

        Log::info("User [ID: {$user->id}] memperbarui kata sandi.");

        $this->passwordSuccessMessage = 'Kata sandi berhasil diperbarui.';
        $this->profileSuccessMessage = null;
        $this->dispatch('password-updated');
    }

    /**
     * Send email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }
}; ?>

<div class="min-h-screen bg-wood-bg text-wood-text">
    {{-- ============================================================
         PAGE HERO (Brown Wood Theme)
         ============================================================ --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-16 sm:pt-40 lg:pt-44 lg:pb-20">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-4 flex items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <span class="font-semibold text-white">Kelola Profil</span>
            </nav>
            <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                <span class="text-wood-secondary-light">✦</span>
                Akun Saya
            </span>
            <h1 class="font-serif text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Kelola Profil
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">
                Kelola informasi data diri, nomor WhatsApp untuk konfirmasi pesanan, dan keamanan akun Anda secara mandiri.
            </p>
        </div>
    </section>

    {{-- ============================================================
         MAIN CONTENT CONTAINER
         ============================================================ --}}
    <section class="py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                {{-- ========================================================
                     LEFT COLUMN: USER SUMMARY CARD
                     ======================================================== --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-28 rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm sm:p-7">
                        {{-- Avatar Preview / Representation --}}
                        <div class="flex flex-col items-center text-center">
                            <div class="relative mb-4">
                                @if ($newAvatar)
                                    <div class="h-28 w-28 overflow-hidden rounded-full ring-4 ring-wood-secondary/30 shadow-md">
                                        <img src="{{ $newAvatar->temporaryUrl() }}" alt="Preview Foto Baru" class="h-full w-full object-cover">
                                    </div>
                                    <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 rounded-full bg-wood-secondary px-2.5 py-0.5 text-[10px] font-bold text-white shadow-sm whitespace-nowrap">
                                        Pratinjau Baru
                                    </span>
                                @elseif ($avatar)
                                    <div class="h-28 w-28 overflow-hidden rounded-full ring-4 ring-wood-border/60 shadow-md">
                                        <img src="{{ Str::startsWith($avatar, ['http://', 'https://']) ? $avatar : asset('storage/' . $avatar) }}" alt="{{ $name }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="flex h-28 w-28 items-center justify-center rounded-full bg-wood-primary text-3xl font-bold text-white ring-4 ring-wood-border/60 shadow-md">
                                        {{ auth()->user()->initials() }}
                                    </div>
                                @endif
                            </div>

                            <h2 class="text-lg font-bold text-wood-text sm:text-xl">{{ $name ?: auth()->user()->name }}</h2>
                            <p class="text-xs text-wood-muted mt-0.5 truncate max-w-full">{{ $email ?: auth()->user()->email }}</p>

                            <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                                @if (auth()->user()->hasRole('admin'))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-wood-primary/10 px-3 py-1 text-xs font-semibold text-wood-primary">
                                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-wood-secondary/15 px-3 py-1 text-xs font-semibold text-wood-primary">
                                        <i class="fa-solid fa-user-check text-[10px]"></i>
                                        Customer Bewole
                                    </span>
                                @endif

                                @if (auth()->user()->hasVerifiedEmail())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-medium text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-clock text-[10px]"></i>
                                        Belum Verifikasi
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Metadata Overview --}}
                        <div class="mt-6 border-t border-wood-border/40 pt-5 space-y-3.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-wood-muted flex items-center gap-2">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm w-4 text-center"></i>
                                    WhatsApp
                                </span>
                                <span class="font-medium text-wood-text truncate max-w-[140px]">
                                    {{ $phone ?: '-' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-wood-muted flex items-center gap-2">
                                    @if ($google_id)
                                        <i class="fa-brands fa-google text-rose-500 text-xs w-4 text-center"></i>
                                    @else
                                        <i class="fa-solid fa-key text-wood-secondary text-xs w-4 text-center"></i>
                                    @endif
                                    Login
                                </span>
                                <span class="font-medium text-wood-text">
                                    {{ $loginMethod }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-wood-muted flex items-center gap-2">
                                    <i class="fa-regular fa-calendar-days text-wood-secondary text-xs w-4 text-center"></i>
                                    Bergabung
                                </span>
                                <span class="font-medium text-wood-text">
                                    {{ $memberSince }}
                                </span>
                            </div>
                        </div>

                        {{-- Quick Link to Orders --}}
                        <div class="mt-6 border-t border-wood-border/40 pt-5">
                            <a
                                href="{{ route('orders.index') }}"
                                wire:navigate
                                class="flex w-full items-center justify-center gap-2 rounded-2xl border border-wood-border/70 bg-wood-bg/60 px-4 py-2.5 text-xs font-semibold text-wood-text hover:bg-wood-primary hover:text-white transition-all duration-300"
                            >
                                <i class="fa-solid fa-box-archive text-xs"></i>
                                Lihat Pesanan Saya
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ========================================================
                     RIGHT COLUMN: FORMS (PROFILE, ACCOUNT, PASSWORD)
                     ======================================================== --}}
                <div class="lg:col-span-8 space-y-8">

                    {{-- ====================================================
                         SECTION 1: INFORMASI PRIBADI
                         ==================================================== --}}
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3.5 border-b border-wood-border/40 pb-5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                                <i class="fa-solid fa-user-pen text-base"></i>
                            </div>
                            <div>
                                <h2 class="font-serif text-xl font-bold text-wood-text sm:text-2xl">Informasi Pribadi</h2>
                                <p class="text-xs text-wood-muted sm:text-sm">Perbarui avatar, nama lengkap, dan nomor WhatsApp Anda.</p>
                            </div>
                        </div>

                        {{-- Success Notification --}}
                        @if ($profileSuccessMessage)
                            <div class="mt-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-xs sm:text-sm font-medium text-emerald-800 transition-all">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                                <span>{{ $profileSuccessMessage }}</span>
                            </div>
                        @endif

                        <form wire:submit.prevent="updateProfileInformation" class="mt-6 space-y-6">
                            {{-- Avatar Management Section --}}
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2.5">
                                    Foto Profil (Avatar)
                                </label>
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    {{-- Avatar Thumbnail --}}
                                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border border-wood-border/60 bg-wood-bg shadow-inner">
                                        @if ($newAvatar)
                                            <img src="{{ $newAvatar->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover">
                                        @elseif ($avatar)
                                            <img src="{{ Str::startsWith($avatar, ['http://', 'https://']) ? $avatar : asset('storage/' . $avatar) }}" alt="{{ $name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-wood-primary text-xl font-bold text-white">
                                                {{ auth()->user()->initials() }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <label class="cursor-pointer inline-flex items-center gap-2 rounded-2xl border border-wood-border bg-wood-bg px-4 py-2.5 text-xs font-semibold text-wood-text hover:bg-wood-primary hover:text-white transition-all shadow-sm">
                                            <i class="fa-solid fa-camera text-xs"></i>
                                            Pilih Foto Baru
                                            <input
                                                type="file"
                                                wire:model="newAvatar"
                                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                                class="hidden"
                                            >
                                        </label>

                                        @if ($avatar)
                                            <button
                                                type="button"
                                                wire:click="deleteAvatar"
                                                wire:confirm="Kembali ke avatar default (inisial nama)?"
                                                class="inline-flex items-center gap-1.5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition-colors shadow-sm"
                                            >
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                                Hapus Foto
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Loading indicator for avatar upload --}}
                                <div wire:loading wire:target="newAvatar" class="mt-2 text-xs text-wood-secondary flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    Mengunggah foto...
                                </div>

                                <p class="mt-2 text-[11px] text-wood-muted">
                                    Format: JPG, PNG, WEBP. Maksimal ukuran file: 2MB. Jika tidak ada foto, avatar inisial akan digunakan otomatis.
                                </p>

                                @error('newAvatar')
                                    <p class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="profile_name" class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-wood-muted">
                                        <i class="fa-regular fa-user text-xs"></i>
                                    </div>
                                    <input
                                        id="profile_name"
                                        type="text"
                                        wire:model="name"
                                        required
                                        autocomplete="name"
                                        placeholder="Masukkan nama lengkap Anda"
                                        class="w-full rounded-2xl border border-wood-border/60 bg-wood-bg/30 pl-11 pr-4 py-3 text-sm text-wood-text placeholder:text-wood-muted/60 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20 transition-all"
                                    >
                                </div>
                                @error('name')
                                    <p class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nomor WhatsApp --}}
                            <div>
                                <label for="profile_phone" class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                    Nomor WhatsApp
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-600">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </div>
                                    <input
                                        id="profile_phone"
                                        type="tel"
                                        wire:model="phone"
                                        autocomplete="tel"
                                        placeholder="Contoh: 081234567890"
                                        class="w-full rounded-2xl border border-wood-border/60 bg-wood-bg/30 pl-11 pr-4 py-3 text-sm text-wood-text placeholder:text-wood-muted/60 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20 transition-all"
                                    >
                                </div>
                                <p class="mt-1.5 text-[11px] text-wood-muted">
                                    Nomor ini akan digunakan tim Bewole untuk mengonfirmasi pesanan furniture dan mengirim update pengerjaan.
                                </p>
                                @error('phone')
                                    <p class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Submit Button --}}
                            <div class="flex items-center justify-end pt-2">
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-wood-primary px-6 py-3 text-sm font-semibold text-white shadow-md shadow-wood-primary/20 hover:bg-wood-primary-dark transition-all duration-300 disabled:opacity-50 cursor-pointer"
                                    data-test="update-profile-button"
                                >
                                    <span wire:loading.remove wire:target="updateProfileInformation">
                                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                                        Simpan Profil
                                    </span>
                                    <span wire:loading wire:target="updateProfileInformation" class="flex items-center gap-2">
                                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ====================================================
                         SECTION 2: INFORMASI AKUN
                         ==================================================== --}}
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3.5 border-b border-wood-border/40 pb-5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                                <i class="fa-solid fa-id-card text-base"></i>
                            </div>
                            <div>
                                <h2 class="font-serif text-xl font-bold text-wood-text sm:text-2xl">Informasi Akun</h2>
                                <p class="text-xs text-wood-muted sm:text-sm">Data identitas autentikasi akun Anda pada sistem Bewole.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            {{-- Email Display Card --}}
                            <div class="rounded-2xl border border-wood-border/50 bg-wood-bg/40 p-4">
                                <div class="flex items-center justify-between text-xs text-wood-muted mb-1.5">
                                    <span class="font-semibold uppercase tracking-wider text-[10px]">Email Akun</span>
                                    <i class="fa-regular fa-envelope text-wood-secondary"></i>
                                </div>
                                <p class="text-sm font-bold text-wood-text truncate" title="{{ $email }}">{{ $email }}</p>
                                <div class="mt-2.5">
                                    @if (auth()->user()->hasVerifiedEmail())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100/80 px-2 py-0.5 text-[10px] font-semibold text-emerald-800">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100/80 px-2 py-0.5 text-[10px] font-semibold text-amber-800">
                                            <i class="fa-solid fa-clock text-[9px]"></i>
                                            Belum Diverifikasi
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-[10px] text-wood-muted leading-tight">
                                    Digunakan untuk autentikasi dan bukti transaksi.
                                </p>
                            </div>

                            {{-- Login Method Card --}}
                            <div class="rounded-2xl border border-wood-border/50 bg-wood-bg/40 p-4">
                                <div class="flex items-center justify-between text-xs text-wood-muted mb-1.5">
                                    <span class="font-semibold uppercase tracking-wider text-[10px]">Metode Login</span>
                                    @if ($google_id)
                                        <i class="fa-brands fa-google text-rose-500"></i>
                                    @else
                                        <i class="fa-solid fa-key text-wood-secondary"></i>
                                    @endif
                                </div>
                                <p class="text-sm font-bold text-wood-text">{{ $loginMethod }}</p>
                                <div class="mt-2.5">
                                    @if ($google_id)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100/80 px-2 py-0.5 text-[10px] font-semibold text-blue-800">
                                            OAuth Provider
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-wood-primary/10 px-2 py-0.5 text-[10px] font-semibold text-wood-primary">
                                            Kredensial Lokal
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-[10px] text-wood-muted leading-tight">
                                    Metode autentikasi yang digunakan saat masuk.
                                </p>
                            </div>

                            {{-- Member Since Card --}}
                            <div class="rounded-2xl border border-wood-border/50 bg-wood-bg/40 p-4">
                                <div class="flex items-center justify-between text-xs text-wood-muted mb-1.5">
                                    <span class="font-semibold uppercase tracking-wider text-[10px]">Bergabung Sejak</span>
                                    <i class="fa-regular fa-calendar-check text-wood-secondary"></i>
                                </div>
                                <p class="text-sm font-bold text-wood-text">{{ $memberSince }}</p>
                                <div class="mt-2.5">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-wood-secondary/20 px-2 py-0.5 text-[10px] font-semibold text-wood-primary">
                                        Pelanggan Aktif
                                    </span>
                                </div>
                                <p class="mt-2 text-[10px] text-wood-muted leading-tight">
                                    Terdaftar di Bewole Jepara Furniture.
                                </p>
                            </div>
                        </div>

                        {{-- Unverified Email notice --}}
                        @if ($this->hasUnverifiedEmail)
                            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50/90 p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-2 text-xs font-medium text-amber-800">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                        <span>Alamat email Anda belum diverifikasi.</span>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click.prevent="resendVerificationNotification"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-wood-primary hover:underline cursor-pointer"
                                    >
                                        Kirim Ulang Link Verifikasi
                                    </button>
                                </div>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-xs font-semibold text-emerald-700">
                                        Link verifikasi baru telah dikirim ke alamat email Anda.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- ====================================================
                         SECTION 3: KEAMANAN AKUN (UBAH KATA SANDI)
                         ==================================================== --}}
                    <div
                        x-data="{ showCurrent: false, showNew: false, showConfirm: false }"
                        class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm sm:p-8"
                    >
                        <div class="flex items-center gap-3.5 border-b border-wood-border/40 pb-5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                                <i class="fa-solid fa-shield-halved text-base"></i>
                            </div>
                            <div>
                                <h2 class="font-serif text-xl font-bold text-wood-text sm:text-2xl">Keamanan Akun</h2>
                                <p class="text-xs text-wood-muted sm:text-sm">Ubah kata sandi akun Anda secara berkala untuk menjaga keamanan.</p>
                            </div>
                        </div>

                        {{-- Success Notification --}}
                        @if ($passwordSuccessMessage)
                            <div class="mt-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-xs sm:text-sm font-medium text-emerald-800 transition-all">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                                <span>{{ $passwordSuccessMessage }}</span>
                            </div>
                        @endif

                        {{-- Notice if Google account has no local password yet --}}
                        @if (! $hasPassword && $google_id)
                            <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50/90 p-4 text-xs sm:text-sm text-blue-900 flex items-start gap-3">
                                <i class="fa-brands fa-google text-rose-500 text-base mt-0.5 shrink-0"></i>
                                <div>
                                    <p class="font-bold">Akun Terhubung dengan Google</p>
                                    <p class="mt-0.5 text-xs text-blue-800">
                                        Akun Anda dibuat melalui Google OAuth. Anda dapat membuat kata sandi di bawah ini jika ingin memiliki opsi masuk menggunakan email dan kata sandi.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">
                            {{-- Field 1: Kata Sandi Saat Ini (Hanya jika user memiliki password lokal) --}}
                            @if ($hasPassword)
                                <div>
                                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                        Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-wood-muted">
                                            <i class="fa-solid fa-lock text-xs"></i>
                                        </div>
                                        <input
                                            id="current_password"
                                            :type="showCurrent ? 'text' : 'password'"
                                            wire:model="current_password"
                                            autocomplete="current-password"
                                            placeholder="Masukkan kata sandi saat ini"
                                            class="w-full rounded-2xl border border-wood-border/60 bg-wood-bg/30 pl-11 pr-11 py-3 text-sm text-wood-text placeholder:text-wood-muted/60 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20 transition-all"
                                        >
                                        <button
                                            type="button"
                                            @click="showCurrent = !showCurrent"
                                            aria-label="Tampilkan atau sembunyikan kata sandi saat ini"
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-wood-muted hover:text-wood-text transition-colors cursor-pointer"
                                        >
                                            <i class="fa-solid" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Field 2: Kata Sandi Baru --}}
                            <div>
                                <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                    Kata Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-wood-muted">
                                        <i class="fa-solid fa-key text-xs"></i>
                                    </div>
                                    <input
                                        id="new_password"
                                        :type="showNew ? 'text' : 'password'"
                                        wire:model="password"
                                        autocomplete="new-password"
                                        placeholder="Minimal 8 karakter"
                                        class="w-full rounded-2xl border border-wood-border/60 bg-wood-bg/30 pl-11 pr-11 py-3 text-sm text-wood-text placeholder:text-wood-muted/60 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20 transition-all"
                                    >
                                    <button
                                        type="button"
                                        @click="showNew = !showNew"
                                        aria-label="Tampilkan atau sembunyikan kata sandi baru"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-wood-muted hover:text-wood-text transition-colors cursor-pointer"
                                    >
                                        <i class="fa-solid" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <p class="mt-1.5 text-[11px] text-wood-muted">
                                    Minimal 8 karakter. Huruf besar dan simbol bersifat opsional (tidak wajib sama dengan kata sandi saat ini).
                                </p>
                                @error('password')
                                    <p class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field 3: Konfirmasi Kata Sandi Baru --}}
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-wood-muted">
                                        <i class="fa-solid fa-circle-check text-xs"></i>
                                    </div>
                                    <input
                                        id="password_confirmation"
                                        :type="showConfirm ? 'text' : 'password'"
                                        wire:model="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Ketik ulang kata sandi baru"
                                        class="w-full rounded-2xl border border-wood-border/60 bg-wood-bg/30 pl-11 pr-11 py-3 text-sm text-wood-text placeholder:text-wood-muted/60 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20 transition-all"
                                    >
                                    <button
                                        type="button"
                                        @click="showConfirm = !showConfirm"
                                        aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-wood-muted hover:text-wood-text transition-colors cursor-pointer"
                                    >
                                        <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Submit Button --}}
                            <div class="flex items-center justify-end pt-2">
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-wood-primary px-6 py-3 text-sm font-semibold text-white shadow-md shadow-wood-primary/20 hover:bg-wood-primary-dark transition-all duration-300 disabled:opacity-50 cursor-pointer"
                                >
                                    <span wire:loading.remove wire:target="updatePassword">
                                        <i class="fa-solid fa-key text-xs"></i>
                                        Ubah Kata Sandi
                                    </span>
                                    <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                        Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
