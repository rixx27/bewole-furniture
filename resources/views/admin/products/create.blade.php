@php
    $title = 'Tambah Produk';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('admin.products.index') }}" class="hover:text-primary transition-colors">Produk</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">{{ $title }}</h2>
        <p class="mt-1 text-sm text-text-secondary">Tambahkan produk furniture baru.</p>
    </div>

    {{-- Form Container --}}
    <div class="max-w-4xl xl:max-w-5xl">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- ======================== --}}
            {{-- Panel Informasi Dasar --}}
            {{-- ======================== --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Informasi Dasar</h3>
                <p class="text-xs text-text-muted mb-5">Data utama produk furniture.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id"
                                name="category_id"
                                class="w-full rounded-lg border {{ $errors->has('category_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary outline-hidden ring-0 transition-colors">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Produk --}}
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Misal: Kursi Tamu Modern"
                               class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Slug
                        </label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old('slug') }}"
                               placeholder="Kosongkan untuk generate otomatis"
                               class="w-full rounded-lg border {{ $errors->has('slug') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('slug')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Kosongkan untuk menghasilkan slug otomatis dari nama produk.</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full rounded-lg border {{ $errors->has('status') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary outline-hidden ring-0 transition-colors">
                            <option value="active" selected>Tersedia</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                {{-- END grid --}}

                {{-- Deskripsi Singkat --}}
                <div class="mt-5">
                    <label for="short_description" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Deskripsi Singkat <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="short_description"
                           name="short_description"
                           value="{{ old('short_description') }}"
                           placeholder="Deskripsi singkat produk (maks. 500 karakter)"
                           class="w-full rounded-lg border {{ $errors->has('short_description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                    @error('short_description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi Lengkap --}}
                <div class="mt-5">
                    <label for="description" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Deskripsi Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="5"
                              placeholder="Deskripsi lengkap produk..."
                              class="w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            {{-- END Panel Informasi Dasar --}}

            {{-- ======================== --}}
            {{-- Panel Harga, Pilihan Meubel & Stok --}}
            {{-- ======================== --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Harga, Pilihan Meubel & Stok</h3>
                <p class="text-xs text-text-muted mb-5">Atur harga untuk produk unfinished, finished, diskon, dan ketersediaan stok.</p>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    {{-- Harga Unit Unfinished --}}
                    <div>
                        <label for="price" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Harga Unit Unfinished (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="price"
                               name="price"
                               inputmode="numeric"
                               value="{{ old('price') }}"
                               placeholder="1.500.000"
                               autocomplete="off"
                               class="w-full rounded-lg border {{ $errors->has('price') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Harga dasar produk kayu unfinished.</p>
                    </div>

                    {{-- Harga Unit Finished --}}
                    <div>
                        <label for="price_matang" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Harga Unit Finished (Rp)
                        </label>
                        <input type="text"
                               id="price_matang"
                               name="price_matang"
                               inputmode="numeric"
                               value="{{ old('price_matang') }}"
                               placeholder="1.800.000"
                               autocomplete="off"
                               class="w-full rounded-lg border {{ $errors->has('price_matang') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('price_matang')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Harga unit finished (siap pakai). Kosongkan jika sama.</p>
                    </div>

                    {{-- Diskon --}}
                    <div>
                        <label for="discount_percentage" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Diskon (%)
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="discount_percentage"
                                   name="discount_percentage"
                                   value="{{ old('discount_percentage') }}"
                                   placeholder="0"
                                   min="0"
                                   max="100"
                                   class="w-full rounded-lg border {{ $errors->has('discount_percentage') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 pr-8 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-text-muted">%</span>
                        </div>
                        @error('discount_percentage')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Kosongkan atau 0 jika tidak ada diskon.</p>
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label for="stock" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="stock"
                               name="stock"
                               value="{{ old('stock', 0) }}"
                               placeholder="0"
                               min="0"
                               class="w-full rounded-lg border {{ $errors->has('stock') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('stock')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                {{-- END grid --}}
            </div>
            {{-- END Panel Harga, Pilihan Meubel & Stok --}}

            {{-- ======================== --}}
            {{-- Panel Detail Produk --}}
            {{-- ======================== --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Detail Produk</h3>
                <p class="text-xs text-text-muted mb-5">Informasi tambahan produk.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    {{-- Bahan --}}
                    <div>
                        <label for="material" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Bahan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="material"
                               name="material"
                               value="{{ old('material') }}"
                               placeholder="Misal: Kayu Jati, Rotan"
                               class="w-full rounded-lg border {{ $errors->has('material') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('material')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dimensi --}}
                    <div>
                        <label for="dimensions" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Dimensi
                        </label>
                        <input type="text"
                               id="dimensions"
                               name="dimensions"
                               value="{{ old('dimensions') }}"
                               placeholder="Misal: 120 x 60 x 75 cm"
                               class="w-full rounded-lg border {{ $errors->has('dimensions') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('dimensions')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Berat --}}
                    <div>
                        <label for="weight" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Berat (kg)
                        </label>
                        <div class="relative">
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   id="weight"
                                   name="weight"
                                   value="{{ old('weight') }}"
                                   placeholder="Misal: 5"
                                   class="w-full rounded-lg border {{ $errors->has('weight') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 pr-10 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-text-muted">kg</span>
                        </div>
                        @error('weight')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Featured --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Produk Unggulan
                        </label>
                        <label class="flex items-center gap-3 mt-2">
                            <input type="checkbox"
                                   name="is_featured"
                                   value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm text-text-secondary">Tandai sebagai produk unggulan</span>
                        </label>
                    </div>
                </div>
                {{-- END grid --}}
            </div>
            {{-- END Panel Detail Produk --}}



            {{-- ======================== --}}
            {{-- Panel Thumbnail --}}
            {{-- ======================== --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Thumbnail</h3>
                <p class="text-xs text-text-muted mb-5">Gambar utama produk (max. 2MB, format: JPG/PNG/WebP).</p>

                <div>
                    <label for="thumbnail" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Thumbnail <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                        {{-- Preview --}}
                        <div id="thumbnail-preview" class="hidden">
                            <img id="thumbnail-image" src="" alt="Preview" class="h-32 w-32 rounded-lg border border-border object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file"
                                   id="thumbnail"
                                   name="thumbnail"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="w-full rounded-lg border {{ $errors->has('thumbnail') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card text-sm text-text-primary file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-dark outline-hidden ring-0 transition-colors">
                            @error('thumbnail')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-text-muted">Pilih gambar utama produk. Rasio 1:1 disarankan.</p>
                        </div>
                    </div>
                    {{-- END flex --}}
                </div>
            </div>
            {{-- END Panel Thumbnail --}}

            {{-- ======================== --}}
            {{-- Panel Galeri --}}
            {{-- ======================== --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6" x-data="galleryUploader()">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Galeri Gambar</h3>
                <p class="text-xs text-text-muted mb-5">Unggah gambar galeri produk (opsional, max. 2MB per gambar, format: JPG/PNG/WebP). Anda bisa menambahkan foto satu per satu atau beberapa sekaligus.</p>

                {{-- New Gallery Upload Area --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-text-muted">
                            Foto Galeri Pilihan
                        </label>
                        <span class="text-xs font-medium text-primary" x-show="items.length > 0" x-text="items.length + ' foto dipilih'"></span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        {{-- Uploaded new items preview --}}
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="relative group rounded-lg border border-border bg-bg-secondary/40 overflow-hidden shadow-2xs aspect-square flex flex-col">
                                <img :src="item.preview" class="w-full h-full object-cover">
                                
                                {{-- Number badge --}}
                                <span class="absolute top-1.5 left-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white shadow">
                                    <span x-text="index + 1"></span>
                                </span>

                                {{-- Remove button --}}
                                <button type="button"
                                        @click="removeItem(item.id)"
                                        title="Hapus foto ini"
                                        class="absolute top-1.5 right-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>

                                {{-- File name label on hover --}}
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-1.5 pt-3">
                                    <p class="text-[10px] text-white truncate text-center" x-text="item.name"></p>
                                </div>
                            </div>
                        </template>

                        {{-- Add Photo Slot / Button --}}
                        <label class="cursor-pointer border-2 border-dashed border-border hover:border-primary rounded-lg aspect-square flex flex-col items-center justify-center p-3 text-center transition-colors bg-card hover:bg-primary/5 group">
                            <input type="file" accept="image/jpeg,image/png,image/webp" multiple @change="handleFiles($event)" class="hidden">
                            <div class="w-9 h-9 rounded-full bg-primary/10 group-hover:bg-primary/20 text-primary flex items-center justify-center mb-1.5 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-text-primary group-hover:text-primary transition-colors">+ Tambah Foto</span>
                            <span class="text-[10px] text-text-muted mt-0.5 leading-tight">Satu per satu / Banyak</span>
                        </label>
                    </div>

                    @error('gallery')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @error('gallery.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    {{-- Hidden file inputs container for submitting to backend --}}
                    <div x-ref="inputContainer" class="hidden"></div>
                </div>
            </div>
            {{-- END Panel Galeri --}}

            {{-- ======================== --}}
            {{-- Actions --}}
            {{-- ======================== --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Produk
                </button>
                <a href="{{ route('admin.products.index') }}"
                   class="rounded-lg border border-border bg-card px-6 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>
    {{-- END Form Container --}}

    @push('scripts')
    <script>
        // Gallery Uploader with dynamic individual file inputs (allows uploading one-by-one or multiple)
        function galleryUploader() {
            return {
                items: [],
                counter: 0,
                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    if (!files.length) return;

                    files.forEach(file => {
                        this.counter++;
                        const itemId = 'gallery_file_' + this.counter;

                        // Create hidden input for form submission
                        const container = this.$refs.inputContainer;
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.name = 'gallery[]';
                        input.id = itemId;
                        input.style.display = 'none';

                        // Transfer file via DataTransfer
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        container.appendChild(input);

                        // Read preview
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.items.push({
                                id: itemId,
                                name: file.name,
                                preview: e.target.result,
                            });
                        };
                        reader.readAsDataURL(file);
                    });

                    // Reset picker value so re-selecting same file triggers change
                    event.target.value = '';
                },
                removeItem(itemId) {
                    const el = document.getElementById(itemId);
                    if (el) el.remove();
                    this.items = this.items.filter(item => item.id !== itemId);
                }
            };
        }

        // ============================
        // Thumbnail Preview
        // ============================
        document.getElementById('thumbnail')?.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(ev) {
                    document.getElementById('thumbnail-image').src = ev.target.result;
                    document.getElementById('thumbnail-preview').classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            }
        });

        // ===============================
        // Format Harga Rupiah
        // ===============================
        const currencyInputs = ['price', 'price_matang'];

        currencyInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                // Initial format if value exists
                if (el.value) {
                    let digits = el.value.replace(/\D/g, '');
                    if (digits) el.value = Number(digits).toLocaleString('id-ID');
                }

                // Format saat mengetik
                el.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    this.value = value === '' ? '' : Number(value).toLocaleString('id-ID');
                });
            }
        });

        // Sebelum submit kirim angka asli (tanpa titik)
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                currencyInputs.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && el.value) {
                        el.value = el.value.replace(/\./g, '');
                    }
                });
            });
        }
    </script>
    @endpush

</x-layouts::admin>

