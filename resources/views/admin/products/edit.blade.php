@php
    $title = 'Ubah Produk';
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
        <p class="mt-1 text-sm text-text-secondary">Perbarui informasi produk "{{ $product->name }}".</p>
    </div>

    {{-- Form --}}
    <div class="max-w-4xl xl:max-w-5xl">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Panel Informasi Dasar --}}
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
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                               value="{{ old('name', $product->name) }}"
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
                               value="{{ old('slug', $product->slug) }}"
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
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="pre_order" {{ old('status', $product->status) == 'pre_order' ? 'selected' : '' }}>Pre Order</option>
                            <option value="sold_out" {{ old('status', $product->status) == 'sold_out' ? 'selected' : '' }}>Habis Terjual</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi Singkat --}}
                <div class="mt-5">
                    <label for="short_description" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Deskripsi Singkat <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="short_description"
                           name="short_description"
                           value="{{ old('short_description', $product->short_description) }}"
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
                              class="w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Panel Harga & Stok --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Harga & Stok</h3>
                <p class="text-xs text-text-muted mb-5">Informasi harga dan ketersediaan stok.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                    {{-- Harga Asli --}}
                    <div>
                        <label for="price" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Harga Asli (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                        id="price"
                        name="price"
                        value="{{ old('price', number_format($product->price, 0, ',', '.')) }}"
                        placeholder="2.000.000"
                        autocomplete="off"
                        class="w-full rounded-lg border {{ $errors->has('price') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
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
                                   value="{{ old('discount_percentage', $product->discount_percentage) }}"
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
                               value="{{ old('stock', $product->stock) }}"
                               placeholder="0"
                               min="0"
                               class="w-full rounded-lg border {{ $errors->has('stock') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('stock')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Panel Detail Produk --}}
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
                               value="{{ old('material', $product->material) }}"
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
                               value="{{ old('dimensions', $product->dimensions) }}"
                               placeholder="Misal: 120 x 60 x 75 cm"
                               class="w-full rounded-lg border {{ $errors->has('dimensions') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('dimensions')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Berat --}}
                    <div>
                        <label for="weight" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Berat
                        </label>
                        <input type="text"
                               id="weight"
                               name="weight"
                               value="{{ old('weight', $product->weight) }}"
                               placeholder="Misal: 5 kg"
                               class="w-full rounded-lg border {{ $errors->has('weight') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
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
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm text-text-secondary">Tandai sebagai produk unggulan</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Panel Thumbnail --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Thumbnail</h3>
                <p class="text-xs text-text-muted mb-5">Gambar utama produk (max. 2MB, format: JPG/PNG/WebP). Kosongkan jika tidak ingin mengubah.</p>

                <div>
                    <label for="thumbnail" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Thumbnail
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                        {{-- Current Thumbnail --}}
                        @if ($product->thumbnail)
                            <div class="shrink-0">
                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                     alt="{{ $product->name }}"
                                     class="h-32 w-32 rounded-lg border border-border object-cover">
                            </div>
                        @endif
                        {{-- Preview new --}}
                        <div id="thumbnail-preview" class="{{ $product->thumbnail ? '' : 'hidden' }}">
                            <img id="thumbnail-image" src="" alt="Preview Baru" class="h-32 w-32 rounded-lg border border-border object-cover">
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
                            <p class="mt-1 text-xs text-text-muted">Biarkan kosong jika tidak ingin mengubah thumbnail.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Galeri --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Galeri Gambar</h3>
                <p class="text-xs text-text-muted mb-5">Kelola gambar galeri produk. Klik gambar untuk menghapus (opsional, max. 2MB per gambar, format: JPG/PNG/WebP).</p>

                {{-- Existing Gallery --}}
                @if ($product->images->count() > 0)
                    <div class="mb-4">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-muted">Galeri Saat Ini</p>
                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                            @foreach ($product->images as $image)
                                <div class="relative group" x-data="{ confirmDelete: false }">
                                    <img src="{{ asset('storage/' . $image->image) }}"
                                         alt="Galeri {{ $loop->iteration }}"
                                         class="h-24 w-full rounded-lg border border-border object-cover">
                                    <button type="button"
                                            x-on:click="confirmDelete = !confirmDelete"
                                            class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-xs transition-opacity group-hover:opacity-100 hover:bg-red-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    {{-- Confirm delete --}}
                                    <div x-show="confirmDelete"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/60 backdrop-blur-sm">
                                        <div class="text-center">
                                            <p class="mb-2 text-xs text-white">Hapus gambar?</p>
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                        x-on:click="confirmDelete = false"
                                                        class="rounded bg-gray-500 px-2 py-1 text-xs text-white hover:bg-gray-600">Batal</button>
                                                <label class="flex cursor-pointer items-center gap-1 rounded bg-red-500 px-2 py-1 text-xs text-white hover:bg-red-600">
                                                    <input type="checkbox"
                                                           name="deleted_images[]"
                                                           value="{{ $image->id }}"
                                                           x-on:change="confirmDelete = false"
                                                           class="hidden">
                                                    Ya, Hapus
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="gallery" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Tambah Gambar Baru
                    </label>
                    <input type="file"
                           id="gallery"
                           name="gallery[]"
                           multiple
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full rounded-lg border {{ $errors->has('gallery') || $errors->has('gallery.*') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card text-sm text-text-primary file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-dark outline-hidden ring-0 transition-colors">
                    @error('gallery')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @error('gallery.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-text-muted">Pilih gambar baru untuk ditambahkan ke galeri.</p>

                    {{-- Gallery Preview --}}
                    <div id="gallery-preview" class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6"></div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Perbarui Produk
                </button>
                <a href="{{ route('admin.products.index') }}"
                   class="rounded-lg border border-border bg-card px-6 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Thumbnail preview
        document.getElementById('thumbnail')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const preview = document.getElementById('thumbnail-preview');
                    const img = document.getElementById('thumbnail-image');
                    img.src = ev.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Gallery preview
        document.getElementById('gallery')?.addEventListener('change', function(e) {
            const preview = document.getElementById('gallery-preview');
            preview.innerHTML = '';
            Array.from(e.target.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${ev.target.result}" alt="Gallery ${index + 1}"
                             class="h-24 w-full rounded-lg border border-border object-cover">
                        <span class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-xs">
                            ${index + 1}
                        </span>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
    @endpush

</x-layouts::admin>

