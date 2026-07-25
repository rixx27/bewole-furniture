@php
    $title = 'Ubah Hero';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('admin.hero-banners.index') }}" class="hover:text-primary transition-colors">Hero Banner</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">{{ $title }}</h2>
        <p class="mt-1 text-sm text-text-secondary">Perbarui hero banner "{{ $hero->title }}".</p>
    </div>

    {{-- Form --}}
    <div class="max-w-4xl xl:max-w-5xl">
        <form method="POST" action="{{ route('admin.hero-banners.update', $hero) }}" enctype="multipart/form-data"
              x-data="heroForm()"
              x-init="init()">
            @csrf
            @method('PUT')

            {{-- Panel Konten Hero --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Konten Hero</h3>
                <p class="text-xs text-text-muted mb-5">Informasi utama yang akan ditampilkan di hero section.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    {{-- Judul Hero --}}
                    <div>
                        <label for="title" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Judul Hero <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               x-model="form.title"
                               value="{{ old('title', $hero->title) }}"
                               placeholder="Misal: Koleksi Furniture Premium"
                               class="w-full rounded-lg border {{ $errors->has('title') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sub Judul --}}
                    <div>
                        <label for="subtitle" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Sub Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="subtitle"
                               name="subtitle"
                               x-model="form.subtitle"
                               value="{{ old('subtitle', $hero->subtitle) }}"
                               placeholder="Misal: Temukan furnitur terbaik untuk rumah Anda"
                               class="w-full rounded-lg border {{ $errors->has('subtitle') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('subtitle')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Badge --}}
                    <div>
                        <label for="badge_text" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Teks Badge
                        </label>
                        <input type="text"
                               id="badge_text"
                               name="badge_text"
                               x-model="form.badge_text"
                               value="{{ old('badge_text', $hero->badge_text) }}"
                               placeholder="Misal: New Collection"
                               class="w-full rounded-lg border {{ $errors->has('badge_text') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('badge_text')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Teks kecil yang muncul di atas judul (opsional).</p>
                    </div>

                    {{-- Posisi Teks --}}
                    <div>
                        <label for="text_position" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Posisi Teks <span class="text-red-500">*</span>
                        </label>
                        <select id="text_position"
                                name="text_position"
                                x-model="form.text_position"
                                class="w-full rounded-lg border {{ $errors->has('text_position') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary outline-hidden ring-0 transition-colors">
                            <option value="left" {{ old('text_position', $hero->text_position) == 'left' ? 'selected' : '' }}>Kiri</option>
                            <option value="center" {{ old('text_position', $hero->text_position) == 'center' ? 'selected' : '' }}>Tengah</option>
                            <option value="right" {{ old('text_position', $hero->text_position) == 'right' ? 'selected' : '' }}>Kanan</option>
                        </select>
                        @error('text_position')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Panel Tombol --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Tombol</h3>
                <p class="text-xs text-text-muted mb-5">Konfigurasi tombol aksi pada hero.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    {{-- Tombol Utama --}}
                    <div>
                        <label for="primary_button_text" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Teks Tombol Utama <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="primary_button_text"
                               name="primary_button_text"
                               x-model="form.primary_button_text"
                               value="{{ old('primary_button_text', $hero->primary_button_text) }}"
                               placeholder="Misal: Lihat Koleksi"
                               class="w-full rounded-lg border {{ $errors->has('primary_button_text') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('primary_button_text')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="primary_button_link" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Link Tombol Utama <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="primary_button_link"
                               name="primary_button_link"
                               x-model="form.primary_button_link"
                               value="{{ old('primary_button_link', $hero->primary_button_link) }}"
                               placeholder="Misal: /products"
                               class="w-full rounded-lg border {{ $errors->has('primary_button_link') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('primary_button_link')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol Kedua --}}
                    <div>
                        <label for="secondary_button_text" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Teks Tombol Kedua
                        </label>
                        <input type="text"
                               id="secondary_button_text"
                               name="secondary_button_text"
                               x-model="form.secondary_button_text"
                               value="{{ old('secondary_button_text', $hero->secondary_button_text) }}"
                               placeholder="Misal: Pelajari Lebih Lanjut"
                               class="w-full rounded-lg border {{ $errors->has('secondary_button_text') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('secondary_button_text')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="secondary_button_link" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Link Tombol Kedua
                        </label>
                        <input type="text"
                               id="secondary_button_link"
                               name="secondary_button_link"
                               x-model="form.secondary_button_link"
                               value="{{ old('secondary_button_link', $hero->secondary_button_link) }}"
                               placeholder="Misal: /about"
                               class="w-full rounded-lg border {{ $errors->has('secondary_button_link') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('secondary_button_link')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Panel Gambar & Pengaturan --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Gambar & Pengaturan</h3>
                <p class="text-xs text-text-muted mb-5">Upload gambar latar dan atur tampilan hero.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    {{-- Gambar Latar --}}
                    <div>
                        <label for="image" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Gambar Latar
                        </label>
                        <div class="flex items-center gap-4 mb-3">
                            @if ($hero->image)
                                <div class="shrink-0">
                                    <img src="{{ asset('storage/' . $hero->image) }}"
                                         alt="{{ $hero->title }}"
                                         class="h-20 w-32 rounded-lg border border-border object-cover">
                                </div>
                            @endif
                            <div class="text-xs text-text-muted">
                                @if ($hero->image)
                                    Gambar saat ini. Upload baru untuk mengganti.
                                @else
                                    Belum ada gambar.
                                @endif
                            </div>
                        </div>
                        <input type="file"
                               id="image"
                               name="image"
                               accept="image/jpeg,image/png,image/webp"
                               x-on:change="handleImageUpload($event)"
                               class="w-full rounded-lg border {{ $errors->has('image') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card text-sm text-text-primary file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-dark outline-hidden ring-0 transition-colors">
                        @error('image')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Format: JPG, PNG, WebP. Maks: 5MB. Kosongkan jika tidak ingin mengubah.</p>
                    </div>

                    {{-- Opacity Overlay --}}
                    <div>
                        <label for="overlay_opacity" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Opacity Overlay: <span class="font-semibold text-primary" x-text="form.overlay_opacity + '%'">{{ $hero->overlay_opacity }}%</span>
                        </label>
                        <input type="range"
                               id="overlay_opacity"
                               name="overlay_opacity"
                               x-model="form.overlay_opacity"
                               min="0"
                               max="100"
                               value="{{ old('overlay_opacity', $hero->overlay_opacity) }}"
                               class="w-full h-2 rounded-lg appearance-none cursor-pointer bg-border accent-primary">
                        <div class="flex justify-between text-xs text-text-muted mt-1">
                            <span>0% (Transparan)</span>
                            <span>100% (Gelap)</span>
                        </div>
                        @error('overlay_opacity')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Urutan --}}
                    <div>
                        <label for="sort_order" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Urutan <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="sort_order"
                               name="sort_order"
                               x-model="form.sort_order"
                               value="{{ old('sort_order', $hero->sort_order) }}"
                               min="0"
                               class="w-full rounded-lg border {{ $errors->has('sort_order') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('sort_order')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Urutan tampilan hero (semakin kecil semakin awal).</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                x-model="form.status"
                                class="w-full rounded-lg border {{ $errors->has('status') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary outline-hidden ring-0 transition-colors">
                            <option value="inactive" {{ old('status', $hero->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="active" {{ old('status', $hero->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Hanya satu hero yang dapat aktif dalam satu waktu.</p>
                    </div>
                </div>
            </div>

            {{-- LIVE PREVIEW --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden mb-6">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-3 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-text-primary dark:text-white flex items-center gap-2">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Pratinjau Hero
                    </h4>
                    <span class="text-[10px] text-text-muted">Pratinjau langsung</span>
                </div>
                <div class="relative bg-zinc-900 overflow-hidden" style="min-height: 400px;">
                    {{-- Background Image --}}
                    <template x-if="form.imagePreview">
                        <img :src="form.imagePreview" alt="Preview" class="absolute inset-0 w-full h-full object-cover">
                    </template>
                    <template x-if="!form.imagePreview && '{{ $hero->image }}'">
                        <img src="{{ asset('storage/' . $hero->image) }}" alt="{{ $hero->title }}" class="absolute inset-0 w-full h-full object-cover">
                    </template>
                    <template x-if="!form.imagePreview && !'{{ $hero->image }}'">
                        <div class="absolute inset-0 flex items-center justify-center bg-zinc-800">
                            <div class="text-center">
                                <svg class="h-12 w-12 mx-auto text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                                </svg>
                                <p class="text-sm text-zinc-500">Upload gambar untuk melihat pratinjau</p>
                            </div>
                        </div>
                    </template>

                    {{-- Overlay --}}
                    <div class="absolute inset-0"
                         :style="'background-color: rgba(0,0,0,' + (form.overlay_opacity / 100) + ')'">
                    </div>

                    {{-- Content --}}
                    <div class="absolute inset-0 flex items-center px-8 md:px-16 lg:px-24"
                         :class="{
                             'justify-start text-left': form.text_position === 'left',
                             'justify-center text-center': form.text_position === 'center',
                             'justify-end text-right': form.text_position === 'right'
                         }">
                        <div class="max-w-2xl space-y-4">
                            {{-- Badge --}}
                            <template x-if="form.badge_text">
                                <span class="inline-block rounded-full bg-amber-500/20 backdrop-blur-sm px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-amber-300 border border-amber-400/30"
                                      x-text="form.badge_text">
                                </span>
                            </template>

                            {{-- Title --}}
                            <h2 class="text-3xl md:text-5xl font-bold text-white leading-tight"
                                x-text="form.title || 'Judul Hero'">
                            </h2>

                            {{-- Subtitle --}}
                            <p class="text-base md:text-lg text-zinc-200 max-w-xl"
                               :class="form.text_position === 'center' ? 'mx-auto' : ''"
                               x-text="form.subtitle || 'Sub judul hero'">
                            </p>

                            {{-- Buttons --}}
                            <div class="flex flex-wrap gap-3 pt-2"
                                 :class="{
                                     'justify-start': form.text_position === 'left',
                                     'justify-center': form.text_position === 'center',
                                     'justify-end': form.text_position === 'right'
                                 }">
                                {{-- Primary Button --}}
                                <template x-if="form.primary_button_text">
                                    <span class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg"
                                          x-text="form.primary_button_text">
                                    </span>
                                </template>

                                {{-- Secondary Button --}}
                                <template x-if="form.secondary_button_text">
                                    <span class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white hover:bg-white/20 transition-colors"
                                          x-text="form.secondary_button_text">
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Perbarui Hero
                </button>
                <a href="{{ route('admin.hero-banners.index') }}"
                   class="rounded-lg border border-border bg-card px-6 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function heroForm() {
            return {
                form: {
                    title: '{{ old('title', $hero->title) }}',
                    subtitle: '{{ old('subtitle', $hero->subtitle) }}',
                    badge_text: '{{ old('badge_text', $hero->badge_text ?? '') }}',
                    primary_button_text: '{{ old('primary_button_text', $hero->primary_button_text) }}',
                    primary_button_link: '{{ old('primary_button_link', $hero->primary_button_link) }}',
                    secondary_button_text: '{{ old('secondary_button_text', $hero->secondary_button_text ?? '') }}',
                    secondary_button_link: '{{ old('secondary_button_link', $hero->secondary_button_link ?? '') }}',
                    text_position: '{{ old('text_position', $hero->text_position) }}',
                    overlay_opacity: {{ old('overlay_opacity', $hero->overlay_opacity) }},
                    sort_order: {{ old('sort_order', $hero->sort_order) }},
                    status: '{{ old('status', $hero->status) }}',
                    imagePreview: null,
                },
                init() {},
                handleImageUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.form.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        }
    </script>
    @endpush

</x-layouts::admin>

