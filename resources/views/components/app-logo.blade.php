@props([
    'sidebar' => false,
])

@php
    $siteName = \App\Helpers\WebsiteSettings::siteName() ?: config('app.name', 'Bewole Jepara Furniture');
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="$siteName" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md overflow-hidden bg-white/10 dark:bg-neutral-800">
            <x-app-logo-icon class="size-7 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="$siteName" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md overflow-hidden bg-white/10 dark:bg-neutral-800">
            <x-app-logo-icon class="size-7 object-contain" />
        </x-slot>
    </flux:brand>
@endif

