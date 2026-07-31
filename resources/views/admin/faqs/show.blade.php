@php
    $title = 'Detail FAQ';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('admin.faqs.index') }}" class="hover:text-primary transition-colors">FAQ</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">{{ $title }}</h2>
                <p class="mt-1 text-sm text-text-secondary">Informasi lengkap FAQ.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.faqs.edit', $faq) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit FAQ
                </a>
                <a href="{{ route('admin.faqs.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="max-w-4xl">
        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-xs">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-text-primary dark:text-black">{{ $faq->question }}</h3>
                        <p class="text-xs text-text-muted">FAQ #{{ $faq->id }}</p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-5">
                {{-- Jawaban --}}
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-muted">Jawaban</p>
                    <div class="text-sm text-text-secondary leading-relaxed whitespace-pre-line">{{ $faq->answer }}</div>
                </div>

                {{-- Status & Urutan --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-muted">Status</p>
                        @if ($faq->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700 dark:bg-red-950 dark:text-red-300">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-muted">Urutan</p>
                        <p class="text-sm text-text-primary dark:text-white">{{ $faq->sort_order }}</p>
                    </div>
                </div>

                {{-- Informasi Waktu --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-muted">Dibuat Pada</p>
                        <p class="text-sm text-text-primary dark:text-white">{{ $faq->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-text-muted">Diperbarui Pada</p>
                        <p class="text-sm text-text-primary dark:text-white">{{ $faq->updated_at->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="border-t border-border bg-bg-secondary/50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-text-muted">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                        </svg>
                        <span>ID: {{ $faq->id }}</span>
                    </div>
<form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline"
                          x-data
                          x-on:submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus FAQ "{{ $faq->question }}"?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus FAQ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts::admin>
