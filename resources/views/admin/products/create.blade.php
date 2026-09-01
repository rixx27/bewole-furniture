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
            {{-- Panel Harga & Stok --}}
            {{-- ======================== --}}
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
                               inputmode="numeric"
                               value="{{ old('price') }}"
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
            {{-- END Panel Harga & Stok --}}

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
                            Berat
                        </label>
                        <input type="text"
                               id="weight"
                               name="weight"
                               value="{{ old('weight') }}"
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
            {{-- Panel Harga Bahan & Customisasi --}}
            {{-- ======================== --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6"
                 x-data="{
                     seatMaterials: [
                         { id: null, type: 'seat_material', name: 'Kulit', price_per_meter: 25000, is_active: true },
                         { id: null, type: 'seat_material', name: 'Benang', price_per_meter: 5000, is_active: true },
                         { id: null, type: 'seat_material', name: 'Anyaman', price_per_meter: 15000, is_active: true }
                     ],
                     packingMaterials: [
                         { id: null, type: 'packing_material', name: 'Kardus', price_per_meter: 10000, is_active: true },
                         { id: null, type: 'packing_material', name: 'Plastik', price_per_meter: 5000, is_active: true }
                     ],
                     init() {
                          this.seatMaterials.forEach(m => m.price_per_meter = this.formatPrice(m.price_per_meter));
                          this.packingMaterials.forEach(m => m.price_per_meter = this.formatPrice(m.price_per_meter));
                      },
                      formatPrice(val) {
                          if (val === null || val === undefined || val === '') return '';
                          let digits = String(val).replace(/\D/g, '');
                          return digits ? Number(digits).toLocaleString('id-ID') : '';
                      },
                      onPriceInput(mat, event) {
                          let digits = event.target.value.replace(/\D/g, '');
                          mat.price_per_meter = digits ? Number(digits).toLocaleString('id-ID') : '';
                      },
                      addSeatMaterial() {
                         this.seatMaterials.push({ id: null, type: 'seat_material', name: '', price_per_meter: 0, is_active: true });
                     },
                     removeSeatMaterial(index) {
                         this.seatMaterials.splice(index, 1);
                     },
                     addPackingMaterial() {
                         this.packingMaterials.push({ id: null, type: 'packing_material', name: '', price_per_meter: 0, is_active: true });
                     },
                     removePackingMaterial(index) {
                         this.packingMaterials.splice(index, 1);
                     }
                 }">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Harga Bahan & Customisasi</h3>
                <p class="text-xs text-text-muted mb-5">Atur kebutuhan bahan per produk serta harga per meter untuk bahan dudukan dan packing.</p>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-6 pb-6 border-b border-border">
                    {{-- Kebutuhan Bahan Dudukan --}}
                    <div>
                        <label for="seat_material_usage" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Kebutuhan Bahan Dudukan / Produk (Meter)
                        </label>
                        <div class="relative">
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   id="seat_material_usage"
                                   name="seat_material_usage"
                                   value="{{ old('seat_material_usage', '0.8') }}"
                                   placeholder="0.8"
                                   class="w-full rounded-lg border border-border focus:border-primary focus:ring-primary bg-card px-4 py-2.5 text-sm text-text-primary outline-hidden ring-0 transition-colors pr-16">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-text-muted font-medium">Meter</span>
                        </div>
                        <p class="mt-1 text-xs text-text-muted">Estimasi jumlah meter bahan dudukan yang dibutuhkan per unit produk.</p>
                    </div>

                    {{-- Kebutuhan Bahan Packing --}}
                    <div>
                        <label for="packing_material_usage" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Kebutuhan Bahan Packing / Produk (Meter)
                        </label>
                        <div class="relative">
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   id="packing_material_usage"
                                   name="packing_material_usage"
                                   value="{{ old('packing_material_usage', '1.2') }}"
                                   placeholder="1.2"
                                   class="w-full rounded-lg border border-border focus:border-primary focus:ring-primary bg-card px-4 py-2.5 text-sm text-text-primary outline-hidden ring-0 transition-colors pr-16">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-text-muted font-medium">Meter</span>
                        </div>
                        <p class="mt-1 text-xs text-text-muted">Estimasi jumlah meter bahan packing yang dibutuhkan per unit produk.</p>
                    </div>
                </div>

                {{-- BAHAN DUDUKAN --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-text-primary dark:text-white">A. Harga Bahan Dudukan</h4>
                        <button type="button" @click="addSeatMaterial()" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                            + Tambah Bahan Dudukan
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-border">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-bg-secondary text-text-muted uppercase font-bold border-b border-border">
                                <tr>
                                    <th class="px-4 py-3">Nama Bahan</th>
                                    <th class="px-4 py-3">Harga / Meter (Rp)</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <template x-for="(mat, idx) in seatMaterials" :key="idx">
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <input type="hidden" :name="'materials[' + idx + '][type]'" value="seat_material">
                                            <input type="text"
                                                   :name="'materials[' + idx + '][name]'"
                                                   x-model="mat.name"
                                                   placeholder="Nama Bahan (misal: Kulit)"
                                                   required
                                                   class="w-full rounded-md border border-border px-3 py-1.5 text-xs text-text-primary focus:border-primary focus:ring-primary">
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <div class="relative">
                                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-xs text-text-muted font-medium">Rp</span>
                                                <input type="text"
                                                       inputmode="numeric"
                                                       autocomplete="off"
                                                       :name="'materials[' + idx + '][price_per_meter]'"
                                                       x-model="mat.price_per_meter"
                                                       @input="onPriceInput(mat, $event)"
                                                       placeholder="25.000"
                                                       required
                                                       class="w-full rounded-md border border-border pl-8 pr-3 py-1.5 text-xs text-text-primary focus:border-primary focus:ring-primary font-mono font-bold">
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                       :name="'materials[' + idx + '][is_active]'"
                                                       value="1"
                                                       x-model="mat.is_active"
                                                       class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600 relative"></div>
                                                <span class="ml-2 text-xs font-medium" :class="mat.is_active ? 'text-emerald-600 font-semibold' : 'text-gray-400'" x-text="mat.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                            </label>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <button type="button" @click="removeSeatMaterial(idx)" class="text-rose-500 hover:text-rose-700 font-medium text-xs">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BAHAN PACKING --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-text-primary dark:text-white">B. Harga Bahan Packing</h4>
                        <button type="button" @click="addPackingMaterial()" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                            + Tambah Bahan Packing
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-border">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-bg-secondary text-text-muted uppercase font-bold border-b border-border">
                                <tr>
                                    <th class="px-4 py-3">Bahan Packing</th>
                                    <th class="px-4 py-3">Harga / Meter (Rp)</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <template x-for="(mat, idx) in packingMaterials" :key="idx">
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <input type="hidden" :name="'materials[' + (idx + 100) + '][type]'" value="packing_material">
                                            <input type="text"
                                                   :name="'materials[' + (idx + 100) + '][name]'"
                                                   x-model="mat.name"
                                                   placeholder="Nama Packing (misal: Kardus)"
                                                   required
                                                   class="w-full rounded-md border border-border px-3 py-1.5 text-xs text-text-primary focus:border-primary focus:ring-primary">
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <div class="relative">
                                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-xs text-text-muted font-medium">Rp</span>
                                                <input type="text"
                                                       inputmode="numeric"
                                                       autocomplete="off"
                                                       :name="'materials[' + (idx + 100) + '][price_per_meter]'"
                                                       x-model="mat.price_per_meter"
                                                       @input="onPriceInput(mat, $event)"
                                                       placeholder="10.000"
                                                       required
                                                       class="w-full rounded-md border border-border pl-8 pr-3 py-1.5 text-xs text-text-primary focus:border-primary focus:ring-primary font-mono font-bold">
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                       :name="'materials[' + (idx + 100) + '][is_active]'"
                                                       value="1"
                                                       x-model="mat.is_active"
                                                       class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600 relative"></div>
                                                <span class="ml-2 text-xs font-medium" :class="mat.is_active ? 'text-emerald-600 font-semibold' : 'text-gray-400'" x-text="mat.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                            </label>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <button type="button" @click="removePackingMaterial(idx)" class="text-rose-500 hover:text-rose-700 font-medium text-xs">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-white mb-1">Galeri Gambar</h3>
                <p class="text-xs text-text-muted mb-5">Unggah gambar galeri produk (opsional, max. 2MB per gambar, format: JPG/PNG/WebP).</p>

                <div>
                    <label for="gallery" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Gambar Galeri
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
                    <p class="mt-1 text-xs text-text-muted">Pilih minimal 3 gambar. Tahan Ctrl untuk memilih banyak gambar.</p>

                    {{-- Gallery Preview --}}
                    <div id="gallery-preview" class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6"></div>
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

        // ============================
        // Gallery Preview
        // ============================
        document.getElementById('gallery')?.addEventListener('change', function(e) {
            const preview = document.getElementById('gallery-preview');
            preview.innerHTML = '';

            Array.from(e.target.files).forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${ev.target.result}"
                            class="h-24 w-full rounded-lg border border-border object-cover">

                        <span class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow">
                            ${index + 1}
                        </span>
                    `;
                    preview.appendChild(div);
                };

                reader.readAsDataURL(file);
            });
        });

        // ===============================
        // Format Harga Rupiah
        // ===============================
        const priceInput = document.getElementById('price');

        if (priceInput) {
            // Format saat mengetik
            priceInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');

                if (value === '') {
                    this.value = '';
                    return;
                }

                this.value = Number(value).toLocaleString('id-ID');
            });

            // Sebelum submit kirim angka asli (tanpa titik)
            const form = priceInput.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    priceInput.value = priceInput.value.replace(/\./g, '');
                });
            }
        }
    </script>
    @endpush

</x-layouts::admin>

