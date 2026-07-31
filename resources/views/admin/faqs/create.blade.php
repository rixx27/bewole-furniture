@php
    $title = 'Tambah FAQ';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('admin.faqs.index') }}" class="hover:text-primary transition-colors">FAQ</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">{{ $title }}</h2>
        <p class="mt-1 text-sm text-text-secondary">Tambahkan pertanyaan yang sering diajukan pelanggan.</p>
    </div>

    {{-- Form Card --}}
    <div class="max-w-4xl xl:max-w-5xl">
        <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.faqs.store') }}">
                @csrf

                {{-- Pertanyaan --}}
                <div class="mb-5">
                    <label for="question" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Pertanyaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="question"
                           name="question"
                           value="{{ old('question') }}"
                           placeholder="Masukkan pertanyaan yang sering diajukan"
                           class="w-full rounded-lg border {{ $errors->has('question') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                    @error('question')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jawaban --}}
                <div class="mb-5">
                    <label for="answer" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Jawaban <span class="text-red-500">*</span>
                    </label>
                    <textarea id="answer"
                              name="answer"
                              rows="6"
                              placeholder="Masukkan jawaban untuk pertanyaan tersebut"
                              class="w-full rounded-lg border {{ $errors->has('answer') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">{{ old('answer') }}</textarea>
                    @error('answer')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Urutan & Status --}}
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-6">
                    {{-- Urutan --}}
                    <div>
                        <label for="sort_order" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Urutan
                        </label>
                        <input type="number"
                               id="sort_order"
                               name="sort_order"
                               value="{{ old('sort_order', 0) }}"
                               placeholder="0"
                               min="0"
                               class="w-full rounded-lg border {{ $errors->has('sort_order') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('sort_order')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-text-muted">Urutan tampilan FAQ (semakin kecil semakin awal).</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Status
                        </label>
                        <label class="flex items-center gap-3 mt-2">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm text-text-secondary">Aktif</span>
                        </label>
                        <p class="mt-1 text-xs text-text-muted">Nonaktifkan untuk menyembunyikan FAQ dari tampilan publik.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 border-t border-border pt-5">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('admin.faqs.index') }}"
                       class="rounded-lg border border-border bg-card px-5 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts::admin>
