@props([
    'status' => null,
    'class' => 'h-5 w-5',
])

@php
    $statusValue = $status instanceof \App\Enums\OrderStatus ? $status->value : (string) $status;
@endphp

@switch($statusValue)
    @case('pending')
        {{-- Menunggu Konfirmasi -> Clock --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
        </svg>
        @break

    @case('confirmed')
        {{-- Pesanan Dikonfirmasi -> CircleCheck --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        @break

    @case('awaiting_payment')
        {{-- Menunggu Pembayaran -> CreditCard / WalletCards --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="20" height="14" x="2" y="5" rx="2"/>
            <line x1="2" x2="22" y1="10" y2="10"/>
        </svg>
        @break

    @case('payment_received')
        {{-- Pembayaran Diterima -> BadgeCheck --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.78 4.78 4 4 0 0 1-6.74 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76z"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        @break

    @case('in_production')
        {{-- Sedang Diproduksi -> Hammer --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 12-8.5 8.5c-.83.83-2.17.83-3 0 0 0 0 0 0 0-.83-.83-.83-2.17 0-3L12 9"/>
            <path d="M17.64 15 22 10.64"/>
            <path d="m20.91 3.66-3.57-3.57a2 2 0 0 0-2.83 0l-4.95 4.95a2 2 0 0 0 0 2.83l3.57 3.57a2 2 0 0 0 2.83 0l4.95-4.95a2 2 0 0 0 0-2.83z"/>
        </svg>
        @break

    @case('quality_control')
        {{-- Quality Control -> ClipboardCheck --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
            <path d="m9 14 2 2 4-4"/>
        </svg>
        @break

    @case('ready_to_ship')
        {{-- Siap Dikirim -> PackageCheck --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m16 16 2 2 4-4"/>
            <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
            <path d="m7.5 4.27 9 5.15"/>
            <polyline points="3.29 7 12 12 20.71 7"/>
            <line x1="12" x2="12" y1="22" y2="12"/>
        </svg>
        @break

    @case('shipped')
        {{-- Dalam Pengiriman -> Truck --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
            <path d="M15 18H9"/>
            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
            <circle cx="17" cy="18" r="2"/>
            <circle cx="7" cy="18" r="2"/>
        </svg>
        @break

    @case('completed')
        {{-- Pesanan Selesai -> CheckCircle --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        @break

    @case('cancelled')
        {{-- Dibatalkan -> CircleX --}}
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m15 9-6 6"/>
            <path d="m9 9 6 6"/>
        </svg>
        @break

    @default
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" x2="12" y1="8" y2="12"/>
            <line x1="12" x2="12.01" y1="16" y2="16"/>
        </svg>
@endswitch
