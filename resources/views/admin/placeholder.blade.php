@php
    $title = $title ?? __('Halaman');
    $description = $description ?? __('Halaman ini akan segera hadir.');
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <h1 class="text-xl font-bold text-text-primary dark:text-white">{{ $title }}</h1>
        <p class="mt-1 text-sm text-text-secondary">{{ $description }}</p>
    </div>

    {{-- Card Segera Hadir --}}
    <div class="rounded-xl border border-border bg-card p-8 shadow-sm">
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-bg-secondary">
                <svg class="h-10 w-10 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11.42 15.17l-5.01 2.51 2.51-5.01m0 0L16.5 4.5a2.12 2.12 0 013 3l-7.58 7.58z" />
                </svg>
            </div>

            <h3 class="mb-2 text-lg font-semibold text-text-primary dark:text-white">Segera Hadir</h3>
            <p class="mb-6 max-w-md text-sm text-text-secondary">
                {{ $description }}
            </p>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

</x-layouts::admin>

