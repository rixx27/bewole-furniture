@php
    $title = 'Pengaturan Website';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Content --}}
    <livewire:admin.settings.website-settings />

</x-layouts::admin>

