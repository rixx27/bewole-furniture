@php
    $title = 'Profil Perusahaan';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Content --}}
    <livewire:admin.profile.company-profile />

</x-layouts::admin>
