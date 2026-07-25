@php
    $title = 'Ubah Kategori';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-primary transition-colors">Kategori</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-white">{{ $title }}</h2>
        <p class="mt-1 text-sm text-text-secondary">Perbarui informasi kategori "{{ $category->name }}".</p>
    </div>

    {{-- Form Card --}}
    <div class="max-w-2xl">
        <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')

                {{-- Nama Kategori --}}
                <div class="mb-5">
                    <label for="name" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-white">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           placeholder="Masukkan nama kategori"
                           class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-5">
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-white">
                        Slug
                    </label>
                    <input type="text"
                           id="slug"
                           name="slug"
                           value="{{ old('slug', $category->slug) }}"
                           placeholder="contoh: kursi-tamu-modern"
                           class="w-full rounded-lg border {{ $errors->has('slug') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                    @error('slug')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-text-muted">Kosongkan untuk menghasilkan slug otomatis dari nama kategori.</p>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-5">
                    <label for="description" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-white">
                        Deskripsi
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Deskripsi kategori (opsional)"
                              class="w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <label class="flex items-center gap-3">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-border text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-text-primary dark:text-white">Aktif</span>
                    </label>
                    <p class="mt-1 text-xs text-text-muted">Nonaktifkan untuk menyembunyikan kategori dari tampilan publik.</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 border-t border-border pt-5">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Perbarui Kategori
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                       class="rounded-lg border border-border bg-card px-5 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts::admin>
