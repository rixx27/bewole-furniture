@php
    $totalUsers = \App\Models\User::count();
@endphp

@include('admin.dashboard.index', ['totalUsers' => $totalUsers])
