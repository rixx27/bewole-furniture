@php
    $title = 'Ulasan Produk';
@endphp

<x-layouts::admin :title="$title">

    {{-- Alert Success --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1">{{ session('success') }}</span>
            <button x-on:click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Ulasan Produk</h2>
                <p class="mt-1 text-sm text-text-secondary">Kelola visibilitas ulasan dari pelanggan.</p>
            </div>
        </div>
    </div>

    {{-- Livewire Table --}}
    <livewire:admin.review.review-table />

</x-layouts::admin>

