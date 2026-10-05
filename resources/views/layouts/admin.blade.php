<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

@php
        $siteName = App\Helpers\WebsiteSettings::siteName();
        $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    @endphp

    <title>
        {{ filled($title ?? null) ? $title . ' — ' . $siteName : $siteName . ' — Admin' }}
    </title>

    <link rel="icon" href="/favicon.ico?v=2" sizes="any">
    <link rel="icon" href="/favicon.svg?v=2" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">

@fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @fluxAppearance
</head>
<body x-data="{ mobileOpen: false }" x-on:toggle-mobile-sidebar.window="mobileOpen = !mobileOpen" class="bg-bg-primary font-sans text-text-primary antialiased">

    {{-- ============================================
         MOBILE SIDEBAR OVERLAY
         ============================================ --}}
    <div x-show="mobileOpen"
         class="fixed inset-0 z-40 lg:hidden">
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"
             x-on:click="mobileOpen = false">
        </div>

        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 flex w-[280px] flex-col bg-sidebar shadow-2xl">
            <div class="flex h-[72px] items-center justify-between border-b border-white/10 px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                        <span class="text-sm font-bold text-white">B</span>
                    </div>
                    <span class="text-base font-semibold tracking-tight text-sidebar-text">Bewole Jepara Furniture</span>
                </a>
                <button x-on:click="mobileOpen = false" class="rounded-lg p-1.5 text-sidebar-text hover:bg-white/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav x-on:click="if ($event.target.closest('a')) mobileOpen = false" class="sidebar-scroll flex-1 space-y-1 overflow-y-auto px-4 py-6">
                @include('partials.admin-sidebar-menu')
            </nav>
        </div>
    </div>

    {{-- ============================================
         MAIN CONTAINER
         ============================================ --}}
    <div class="flex h-screen w-full min-w-0 overflow-hidden">

        {{-- DESKTOP SIDEBAR --}}
        <aside class="hidden w-[280px] shrink-0 lg:flex lg:flex-col">
            <div class="flex h-full flex-col bg-sidebar shadow-2xl">
                <div class="flex h-[72px] items-center border-b border-white/10 px-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary shadow-lg">
                            <span class="text-lg font-bold text-white">B</span>
                        </div>
                        <div>
                            <span class="text-base font-bold tracking-tight text-white">Bewole Jepara Furniture</span>
                            <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-sidebar-text">Administrator</span>
                        </div>
                    </a>
                </div>

                <nav class="sidebar-scroll flex-1 space-y-1 overflow-y-auto px-4 py-6">
                    @include('partials.admin-sidebar-menu')
                </nav>

                <div class="border-t border-white/10 px-4 py-4">
                    <div x-data="{ open: false }" class="relative">
                        <button x-on:click="open = !open"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover transition-all duration-200">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-sm">
                                {{ auth()->user()->initials() }}
                            </span>
                            <span class="flex-1 truncate text-left">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-full left-0 right-0 mb-2 rounded-xl border border-white/10 bg-sidebar-hover p-1 shadow-xl">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-300 hover:bg-white/10">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

            {{-- Top Navbar --}}
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-border bg-card px-4 shadow-xs lg:px-6">
                <div class="flex items-center gap-3 min-w-0">
                    <button x-on:click="window.dispatchEvent(new CustomEvent('toggle-mobile-sidebar'))"
                            class="flex items-center justify-center rounded-lg p-2 text-text-secondary hover:bg-bg-secondary lg:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                    </button>
                    <div class="hidden lg:block">
                        <h2 class="text-sm font-semibold text-text-primary">{{ $title ?? 'Dashboard' }}</h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm text-text-secondary hover:bg-bg-secondary transition-colors">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <span class="hidden text-xs text-text-muted sm:inline">Cari...</span>
                    </button>

                    <button class="relative flex items-center justify-center rounded-lg p-2 text-text-secondary hover:bg-bg-secondary transition-colors">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                    </button>

                    <div x-data="{ profileOpen: false }" class="relative">
                        <button x-on:click="profileOpen = !profileOpen"
                                class="flex items-center gap-2 rounded-lg p-1.5 text-text-secondary hover:bg-bg-secondary transition-colors">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-sm shrink-0">
                                {{ auth()->user()->initials() }}
                            </span>
                            <svg class="hidden h-4 w-4 sm:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <div x-show="profileOpen"
                             x-on:click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full z-50 mt-2 w-56 origin-top-right rounded-xl border border-border bg-card p-1 shadow-lg">
                            <div class="border-b border-border px-3 py-2">
                                <p class="text-sm font-medium text-text-primary truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-text-secondary truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto min-w-0 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="border-t border-border bg-card px-4 py-3 sm:px-6 shrink-0">
                <div class="flex flex-col gap-1.5 text-center text-xs text-text-muted sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="leading-relaxed">&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
                    <p class="shrink-0 font-medium">Panel Admin v1.0</p>
                </div>
            </footer>
        </div>
    </div>

    {{-- Global Toast Notification --}}
    <div x-data="{ show: false, type: 'success', message: '' }"
         x-on:notify.window="show = true; type = $event.detail.type || 'success'; message = $event.detail.message; setTimeout(() => show = false, 4000)"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         class="fixed right-4 top-4 z-[9999] max-w-sm pointer-events-auto"
         role="alert">
        <div class="flex items-center gap-3 rounded-xl border p-3.5 shadow-xl backdrop-blur-md transition-colors"
             :class="{
                 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950 dark:border-emerald-800 dark:text-emerald-300': type === 'success',
                 'bg-red-50 border-red-200 text-red-800 dark:bg-red-950 dark:border-red-800 dark:text-red-300': type === 'error',
                 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-950 dark:border-blue-800 dark:text-blue-300': type === 'info'
             }">
            <svg x-show="type === 'success'" class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg x-show="type === 'error'" class="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs font-medium leading-normal flex-1" x-text="message"></span>
            <button type="button" x-on:click="show = false" class="text-current opacity-60 hover:opacity-100 p-0.5">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Order Photo Progress Capture Handler --}}
    <script>
        window.orderProgressCapture = function(orderCodeParam) {
            return {
                cameraActive: false,
                processing: false,
                videoStream: null,
                statusMessage: '',
                orderCode: orderCodeParam || '',
                facingMode: 'environment',

                init() {},

                destroy() {
                    this.stopCamera();
                },

                async switchCamera() {
                    this.facingMode = (this.facingMode === 'environment') ? 'user' : 'environment';
                    await this.startCamera();
                },

                async startCamera() {
                    this.cameraActive = true;
                    this.processing = true;
                    this.statusMessage = 'Menghubungkan ke kamera...';

                    if (this.videoStream) {
                        try {
                            this.videoStream.getTracks().forEach(t => t.stop());
                        } catch (e) {}
                        this.videoStream = null;
                    }

                    try {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            throw new Error('Fitur kamera browser tidak didukung atau memerlukan HTTPS/localhost.');
                        }

                        let stream;
                        try {
                            stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: { ideal: this.facingMode },
                                    width: { ideal: 1280 },
                                    height: { ideal: 720 }
                                },
                                audio: false
                            });
                        } catch (err1) {
                            console.warn('Fallback ke default webcam:', err1);
                            stream = await navigator.mediaDevices.getUserMedia({
                                video: true,
                                audio: false
                            });
                        }

                        this.videoStream = stream;
                        this.processing = false;
                        this.statusMessage = '';

                        this.$nextTick(async () => {
                            const video = this.$refs.videoEl;
                            if (video) {
                                video.srcObject = stream;
                                try {
                                    await video.play();
                                } catch (e) {
                                    console.warn('Video play warning:', e);
                                }
                            }
                        });
                    } catch (err) {
                        console.error('Camera access failed:', err);
                        this.stopCamera();
                        alert('Kamera tidak dapat diakses (' + (err.message || 'Izin kamera ditolak') + ').\nSilakan izinkan akses kamera di browser Anda atau gunakan tombol "Unggah foto dari file".');
                    }
                },

                stopCamera() {
                    if (this.videoStream) {
                        try {
                            this.videoStream.getTracks().forEach(track => track.stop());
                        } catch (e) {}
                        this.videoStream = null;
                    }
                    if (this.$refs.videoEl) {
                        this.$refs.videoEl.srcObject = null;
                    }
                    this.cameraActive = false;
                    this.processing = false;
                    this.statusMessage = '';
                },

                async captureFromCamera() {
                    if (!this.videoStream || !this.$refs.videoEl) return;

                    this.processing = true;
                    this.statusMessage = 'Mengambil foto & titik koordinat GPS...';

                    const video = this.$refs.videoEl;
                    const targetW = video.videoWidth || 1280;
                    const targetH = video.videoHeight || 720;

                    const canvas = document.createElement('canvas');
                    canvas.width = targetW;
                    canvas.height = targetH;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, targetW, targetH);

                    this.stopCamera();
                    await this.stampCanvasAndSave(canvas, targetW, targetH);
                },

                async processFile(event) {
                    const file = event.target.files && event.target.files[0];
                    if (!file) return;

                    this.processing = true;
                    this.statusMessage = 'Mendeteksi titik koordinat GPS...';

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = async () => {
                            const maxW = 1280;
                            let targetW = img.width;
                            let targetH = img.height;

                            if (targetW > maxW) {
                                targetH = Math.round((targetH * maxW) / targetW);
                                targetW = maxW;
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = targetW;
                            canvas.height = targetH;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, targetW, targetH);

                            await this.stampCanvasAndSave(canvas, targetW, targetH);
                            event.target.value = '';
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                async stampCanvasAndSave(canvas, targetW, targetH) {
                    let lat = -6.58912;
                    let lng = 110.66782;
                    let locationSource = 'Workshop Jepara';

                    try {
                        if (navigator.geolocation) {
                            const pos = await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition(resolve, reject, {
                                    enableHighAccuracy: true,
                                    timeout: 4000,
                                    maximumAge: 0
                                });
                            });
                            lat = parseFloat(pos.coords.latitude.toFixed(6));
                            lng = parseFloat(pos.coords.longitude.toFixed(6));
                            locationSource = 'GPS Aktif';
                        }
                    } catch (err) {
                        console.warn('GPS fallback:', err);
                    }

                    const ctx = canvas.getContext('2d');
                    const bannerHeight = Math.max(90, Math.round(targetH * 0.16));
                    const bannerY = targetH - bannerHeight;

                    // Watermark background bar
                    ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
                    ctx.fillRect(0, bannerY, targetW, bannerHeight);

                    // Amber top accent line
                    ctx.fillStyle = '#d97706';
                    ctx.fillRect(0, bannerY, targetW, Math.max(3, Math.round(targetW * 0.004)));

                    const now = new Date();
                    const day = String(now.getDate()).padStart(2, '0');
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const timeStr = `${day} ${month} ${year}, ${hours}:${minutes}:${seconds} WIB`;

                    const code = this.orderCode || document.querySelector('[data-order-code]')?.getAttribute('data-order-code') || '';
                    const statusLabel = document.querySelector('input[name="newStatus"]:checked')?.closest('label')?.querySelector('.text-gray-900')?.innerText?.trim() || 'Proses Pengerjaan';

                    const padX = Math.round(targetW * 0.025);
                    const baseFontSize = Math.max(13, Math.round(targetW * 0.02));
                    const smallFontSize = Math.max(11, Math.round(targetW * 0.015));

                    let currY = bannerY + Math.round(bannerHeight * 0.28);

                    ctx.font = `bold ${baseFontSize}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
                    ctx.fillStyle = '#fbbf24';
                    ctx.fillText('🏢 BEWOLE JEPARA FURNITURE — DOKUMENTASI RESMI', padX, currY);

                    currY += Math.round(bannerHeight * 0.25);
                    ctx.font = `bold ${smallFontSize}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(`📦 Pesanan: #${code}  •  Status: ${statusLabel}`, padX, currY);

                    currY += Math.round(bannerHeight * 0.25);
                    ctx.font = `normal ${smallFontSize}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
                    ctx.fillStyle = '#cbd5e1';
                    ctx.fillText(`🕒 ${timeStr}  |  📍 Lat: ${lat}, Long: ${lng} (${locationSource})`, padX, currY);

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

                    if (this.$wire) {
                        this.$wire.set('photoData', dataUrl);
                        this.$wire.set('latitude', lat);
                        this.$wire.set('longitude', lng);
                    }

                    this.processing = false;
                    this.statusMessage = '';
                }
            };
        };

        if (window.Alpine) {
            window.Alpine.data('orderProgressCapture', window.orderProgressCapture);
        } else {
            document.addEventListener('alpine:init', () => {
                window.Alpine.data('orderProgressCapture', window.orderProgressCapture);
            });
        }
    </script>

    @stack('scripts')
    @fluxScripts
</body>
</html>
