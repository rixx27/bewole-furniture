<section
    id="custom-furniture"
    aria-labelledby="custom-furniture-heading"
    class="relative overflow-hidden bg-wood-bg py-20 sm:py-24 lg:py-28"
    x-data="customFurnitureModal('{{ $whatsappNumber }}', @js(auth()->check()), @js(auth()->user()?->name ?? ''), @js(auth()->user()?->phone ?? ''))"
>
    {{-- Ambient background accents (matching theme) --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="animate-blob absolute -top-32 -left-32 h-96 w-96 rounded-full bg-wood-secondary/15 blur-3xl"></div>
        <div class="animate-blob absolute -bottom-40 -right-32 h-[28rem] w-[28rem] rounded-full bg-wood-primary/10 blur-3xl" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- ============================================================
             EDITORIAL TWO-COLUMN LAYOUT
             Desktop/Tablet : Teks kiri | Foto kanan
             Mobile         : Satu kolom (teks → foto)
             ============================================================ --}}
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            {{-- LEFT : Text & CTA --}}
            <div class="order-1">
                {{-- Label --}}
                <div data-reveal data-reveal-delay="0">
                    <span class="mb-5 inline-flex items-center gap-2 rounded-full border border-wood-primary/15 bg-wood-primary/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-wood-primary">
                        <span class="text-wood-secondary">✦</span>
                        {{ $badge }}
                    </span>
                </div>

                {{-- Heading --}}
                <h2
                    id="custom-furniture-heading"
                    data-reveal
                    data-reveal-delay="100"
                    class="font-serif text-3xl font-bold tracking-tight text-wood-text sm:text-4xl lg:text-5xl"
                >
                    {{ $heading }}
                </h2>

                {{-- Deskripsi --}}
                <p
                    data-reveal
                    data-reveal-delay="200"
                    class="mt-6 max-w-xl text-base leading-relaxed text-wood-muted sm:text-lg"
                >
                    {{ $description }}
                </p>

                {{-- CTA Button --}}
                <div data-reveal data-reveal-delay="300" class="mt-8">
                    <button
                        type="button"
                        @click="openModal()"
                        class="group inline-flex w-full items-center justify-center gap-2.5 rounded-full bg-wood-primary px-8 py-4 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:bg-wood-primary-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2 sm:w-auto cursor-pointer"
                    >
                        <span>{{ $buttonText }}</span>
                        <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- RIGHT : Foto Furniture --}}
            <div class="order-2">
                <div
                    data-reveal
                    data-reveal-delay="400"
                    class="group relative aspect-[4/3] w-full max-w-md overflow-hidden rounded-[2rem] border border-wood-border/60 bg-wood-surface shadow-xl shadow-wood-primary/10 sm:max-w-lg lg:ml-auto"
                >
                    @if ($imageUrl)
                        <img
                            src="{{ $imageUrl }}"
                            alt="Custom Furniture Bewole"
                            loading="lazy"
                            class="philosophy-image h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-[1.03]"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-wood-primary/80 to-wood-primary-dark">
                            <span class="font-serif text-6xl font-bold text-white/25">BEWOLE</span>
                        </div>
                    @endif

                    {{-- Overlay tipis untuk efek depth editorial --}}
                    <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-black/25 to-transparent opacity-60" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FORM REQUEST CUSTOM MODAL (Alpine.js)
         ============================================================ --}}
    <template x-teleport="body">
        <div
            x-show="isOpen"
            x-cloak
            @keydown.escape.window="closeModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-8"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
        >
            {{-- Backdrop overlay --}}
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeModal()"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                aria-hidden="true"
            ></div>

            {{-- Modal Content Box --}}
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-6 scale-95"
                class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-[2rem] border border-wood-border bg-wood-surface p-6 shadow-2xl sm:p-8 sidebar-scroll"
            >
                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-wood-border/60 pb-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-wood-secondary">
                            ✦ Konsultasi Gratis
                        </span>
                        <h3 id="modal-title" class="font-serif text-2xl font-bold tracking-tight text-wood-text sm:text-3xl">
                            Request Custom Furniture
                        </h3>
                        <p class="mt-1 text-xs text-wood-muted sm:text-sm">
                            Isi kebutuhan furniture Anda di bawah ini.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="closeModal()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-wood-border/80 bg-wood-bg text-wood-muted transition-colors hover:bg-wood-primary hover:text-white focus:outline-none cursor-pointer"
                        aria-label="Tutup modal"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <form @submit.prevent="submitForm()" class="mt-6 space-y-4">
                    <div x-show="errors.general" x-text="errors.general" class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-semibold text-red-600" x-cloak></div>

                    {{-- Nama --}}
                    <div>
                        <label for="custom-name" class="block text-xs font-bold uppercase tracking-wider text-wood-text sm:text-sm">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="custom-name"
                            x-model="form.name"
                            x-ref="nameInput"
                            placeholder="Contoh: Budi Santoso"
                            class="mt-1.5 w-full rounded-xl border border-wood-border bg-wood-bg px-4 py-3 text-sm text-wood-text placeholder-wood-muted transition-all duration-200 focus:border-wood-secondary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-secondary/20"
                            :class="{ 'border-red-500 ring-2 ring-red-500/20': errors.name }"
                        >
                        <p x-show="errors.name" x-text="errors.name" class="mt-1 text-xs text-red-500" x-cloak></p>
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div>
                        <label for="custom-wa" class="block text-xs font-bold uppercase tracking-wider text-wood-text sm:text-sm">
                            Nomor WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="custom-wa"
                            x-model="form.whatsapp"
                            placeholder="Contoh: 081234567890"
                            class="mt-1.5 w-full rounded-xl border border-wood-border bg-wood-bg px-4 py-3 text-sm text-wood-text placeholder-wood-muted transition-all duration-200 focus:border-wood-secondary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-secondary/20"
                            :class="{ 'border-red-500 ring-2 ring-red-500/20': errors.whatsapp }"
                        >
                        <p x-show="errors.whatsapp" x-text="errors.whatsapp" class="mt-1 text-xs text-red-500" x-cloak></p>
                    </div>

                    {{-- Jenis Furniture --}}
                    <div>
                        <label for="custom-type" class="block text-xs font-bold uppercase tracking-wider text-wood-text sm:text-sm">
                            Jenis Furniture <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="custom-type"
                            x-model="form.furniture_type"
                            placeholder="Contoh: Meja Makan 6 Kursi, Lemari Pakaian 3 Pintu"
                            class="mt-1.5 w-full rounded-xl border border-wood-border bg-wood-bg px-4 py-3 text-sm text-wood-text placeholder-wood-muted transition-all duration-200 focus:border-wood-secondary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-secondary/20"
                            :class="{ 'border-red-500 ring-2 ring-red-500/20': errors.furniture_type }"
                        >
                        <p x-show="errors.furniture_type" x-text="errors.furniture_type" class="mt-1 text-xs text-red-500" x-cloak></p>
                    </div>

                    {{-- Deskripsi Kebutuhan --}}
                    <div>
                        <label for="custom-desc" class="block text-xs font-bold uppercase tracking-wider text-wood-text sm:text-sm">
                            Deskripsi / Kebutuhan Custom <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="custom-desc"
                            x-model="form.description"
                            rows="3"
                            placeholder="Jelaskan detail desain, jenis kayu (misal: Jati Perhutani), finishing (natural/dark), atau keinginan khusus lainnya..."
                            class="mt-1.5 w-full rounded-xl border border-wood-border bg-wood-bg px-4 py-3 text-sm text-wood-text placeholder-wood-muted transition-all duration-200 focus:border-wood-secondary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-secondary/20"
                            :class="{ 'border-red-500 ring-2 ring-red-500/20': errors.description }"
                        ></textarea>
                        <p x-show="errors.description" x-text="errors.description" class="mt-1 text-xs text-red-500" x-cloak></p>
                    </div>

                    {{-- Ukuran / Detail Tambahan --}}
                    <div>
                        <label for="custom-dim" class="block text-xs font-bold uppercase tracking-wider text-wood-text sm:text-sm">
                            Ukuran / Detail Tambahan <span class="text-xs font-normal text-wood-muted">(Opsional)</span>
                        </label>
                        <input
                            type="text"
                            id="custom-dim"
                            x-model="form.dimensions"
                            placeholder="Contoh: Panjang 200 cm x Lebar 90 cm x Tinggi 75 cm"
                            class="mt-1.5 w-full rounded-xl border border-wood-border bg-wood-bg px-4 py-3 text-sm text-wood-text placeholder-wood-muted transition-all duration-200 focus:border-wood-secondary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-secondary/20"
                        >
                    </div>

                    {{-- Upload Foto / Gambar Desain Contoh --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-wood-text sm:text-sm">
                            Foto / Gambar Contoh Desain <span class="text-xs font-normal text-wood-muted">(Opsional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                type="file"
                                id="custom-image-input"
                                x-ref="imageInput"
                                @change="handleFileChange($event)"
                                accept="image/png,image/jpeg,image/webp,image/jpg"
                                class="hidden"
                            >

                            <!-- Area Dropzone / Button Pilih File -->
                            <div
                                x-show="!imagePreview"
                                @click="$refs.imageInput.click()"
                                class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-wood-border/80 bg-wood-bg/60 p-5 text-center transition-all duration-200 hover:border-wood-secondary hover:bg-wood-bg"
                            >
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-wood-surface text-wood-secondary shadow-sm transition-transform duration-200 group-hover:scale-110">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="mt-2.5 text-xs font-semibold text-wood-text sm:text-sm">
                                    Klik untuk upload gambar referensi / sketsa
                                </p>
                                <p class="mt-1 text-[11px] text-wood-muted">
                                    Format: JPG, PNG, WEBP (Maksimal 5MB)
                                </p>
                            </div>

                            <!-- Preview Gambar Jika Sudah Dipilih -->
                            <div x-show="imagePreview" x-cloak class="relative overflow-hidden rounded-2xl border border-wood-border bg-wood-bg p-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-wood-border/60 bg-wood-surface">
                                        <img :src="imagePreview" alt="Preview Gambar Desain" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-semibold text-wood-text sm:text-sm" x-text="imageFile?.name"></p>
                                        <p class="mt-0.5 text-[11px] text-wood-muted" x-text="formatFileSize(imageFile?.size)"></p>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Gambar siap dikirim
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeFile()"
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition-colors hover:bg-red-100 focus:outline-none"
                                        title="Hapus gambar"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <p x-show="errors.image" x-text="errors.image" class="mt-1.5 text-xs text-red-500" x-cloak></p>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-3">
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="group flex w-full items-center justify-center gap-2.5 rounded-full bg-wood-primary px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 hover:bg-wood-primary-dark focus:outline-none focus:ring-2 focus:ring-wood-secondary focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70 cursor-pointer"
                        >
                            <svg x-show="isSubmitting" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Mengunggah & Memproses...' : 'Kirim Request →'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('customFurnitureModal', (adminNumber, isLoggedIn = false, userName = '', userPhone = '') => ({
                    isOpen: false,
                    isSubmitting: false,
                    isLoggedIn: isLoggedIn,
                    userName: userName,
                    userPhone: userPhone,
                    adminNumber: adminNumber || '',
                    imageFile: null,
                    imagePreview: null,
                    form: {
                        name: userName || '',
                        whatsapp: userPhone || '',
                        furniture_type: '',
                        description: '',
                        dimensions: '',
                    },
                    errors: {},

                    openModal() {
                        this.isOpen = true;
                        if (!this.form.name && this.userName) this.form.name = this.userName;
                        if (!this.form.whatsapp && this.userPhone) this.form.whatsapp = this.userPhone;
                        document.body.style.overflow = 'hidden';
                        this.$nextTick(() => {
                            if (this.$refs.nameInput) {
                                this.$refs.nameInput.focus();
                            }
                        });
                    },

                    closeModal() {
                        this.isOpen = false;
                        document.body.style.overflow = '';
                        this.errors = {};
                    },

                    handleFileChange(event) {
                        const file = event.target.files[0];
                        this.errors.image = null;

                        if (!file) return;

                        if (!['image/jpeg', 'image/png', 'image/webp', 'image/jpg'].includes(file.type)) {
                            this.errors.image = 'Format file tidak didukung. Harap upload gambar JPG, PNG, atau WEBP.';
                            return;
                        }

                        if (file.size > 5 * 1024 * 1024) {
                            this.errors.image = 'Ukuran gambar maksimal adalah 5MB.';
                            return;
                        }

                        this.imageFile = file;
                        this.imagePreview = URL.createObjectURL(file);
                    },

                    removeFile() {
                        this.imageFile = null;
                        this.imagePreview = null;
                        if (this.$refs.imageInput) {
                            this.$refs.imageInput.value = '';
                        }
                    },

                    formatFileSize(bytes) {
                        if (!bytes) return '';
                        if (bytes < 1024) return bytes + ' B';
                        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                    },

                    async submitForm() {
                        this.errors = {};

                        if (!this.form.name.trim()) {
                            this.errors.name = 'Nama lengkap wajib diisi.';
                        }
                        if (!this.form.whatsapp.trim()) {
                            this.errors.whatsapp = 'Nomor WhatsApp wajib diisi.';
                        }
                        if (!this.form.furniture_type.trim()) {
                            this.errors.furniture_type = 'Jenis furniture wajib diisi.';
                        }
                        if (!this.form.description.trim()) {
                            this.errors.description = 'Deskripsi kebutuhan wajib diisi.';
                        }

                        if (Object.keys(this.errors).length > 0) {
                            return;
                        }

                        this.isSubmitting = true;

                        try {
                            const formData = new FormData();
                            formData.append('name', this.form.name.trim());
                            formData.append('whatsapp', this.form.whatsapp.trim());
                            formData.append('furniture_type', this.form.furniture_type.trim());
                            formData.append('description', this.form.description.trim());
                            if (this.form.dimensions.trim()) {
                                formData.append('dimensions', this.form.dimensions.trim());
                            }
                            if (this.imageFile) {
                                formData.append('image', this.imageFile);
                            }

                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                            const response = await fetch('{{ route("custom-furniture.store") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: formData
                            });

                            const result = await response.json();

                            if (!response.ok || !result.success) {
                                if (result.errors) {
                                    this.errors = Object.fromEntries(
                                        Object.entries(result.errors).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
                                    );
                                } else {
                                    this.errors.general = result.message || 'Gagal menyimpan request custom. Silakan coba lagi.';
                                }
                                this.isSubmitting = false;
                                return;
                            }

                            const orderCode = result.order_code;
                            const uploadedImageUrl = result.image_url;

                            // Dispatch notification
                            const successMsg = this.isLoggedIn
                                ? `Request custom berhasil dibuat (#${orderCode})! Telah masuk ke daftar Pesanan Saya.`
                                : `Request custom berhasil dibuat (#${orderCode})! Anda dapat melacaknya melalui fitur Lacak Pesanan.`;

                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { type: 'success', message: successMsg }
                            }));

                            // Format pesan WhatsApp dengan kode order resmi
                            const messageLines = [
                                'Halo Admin Bewole Jepara Furniture, saya ingin konsultasi request custom furniture.',
                                '',
                                `*Kode Pesanan: #${orderCode}*`,
                                `Nama: ${this.form.name.trim()}`,
                                `Nomor WhatsApp: ${this.form.whatsapp.trim()}`,
                                `Jenis Furniture: ${this.form.furniture_type.trim()}`,
                                `Deskripsi: ${this.form.description.trim()}`,
                                `Ukuran / Detail: ${this.form.dimensions.trim() || '-'}`,
                            ];

                            if (uploadedImageUrl) {
                                messageLines.push(`Link Foto Referensi Desain: ${uploadedImageUrl}`);
                            }

                            messageLines.push('');
                            messageLines.push('Saya telah mengajukan request ini melalui website Bewole Furniture. Mohon informasi konfirmasi dan estimasi biayanya. Terima kasih!');

                            const messageText = messageLines.join('\n');
                            const targetNumber = this.adminNumber || '6281234567890';
                            const waUrl = `https://wa.me/${targetNumber}?text=${encodeURIComponent(messageText)}`;

                            // Reset form state & tutup modal
                            this.removeFile();
                            this.closeModal();
                            this.form = {
                                name: this.userName || '',
                                whatsapp: this.userPhone || '',
                                furniture_type: '',
                                description: '',
                                dimensions: '',
                            };
                            this.isSubmitting = false;

                            // Buka WhatsApp di tab baru
                            window.open(waUrl, '_blank', 'noopener,noreferrer');
                        } catch (err) {
                            console.error('Submit error:', err);
                            this.errors.general = 'Terjadi kesalahan pada sistem saat memproses request. Silakan coba lagi.';
                            this.isSubmitting = false;
                        }
                    }
                }));
            });
        </script>
    @endpush
@endonce
