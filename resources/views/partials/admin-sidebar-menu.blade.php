{{-- Dashboard --}}
<a href="{{ route('dashboard') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('dashboard'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('dashboard'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    <span>Dashboard</span>
</a>

{{-- Master Data Group --}}
<div class="my-3 border-t border-white/10"></div>
<p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-sidebar-text/60">Master Data</p>

<a href="{{ route('admin.categories.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.categories.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.categories.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
    </svg>
    <span>Kategori</span>
</a>

<a href="{{ route('admin.products.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.products.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.products.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
    </svg>
    <span>Produk</span>
</a>

{{-- Konten Group --}}
<div class="my-3 border-t border-white/10"></div>
<p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-sidebar-text/60">Konten</p>

<a href="{{ route('admin.hero-banners.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.hero-banners.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.hero-banners.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
    </svg>
    <span>Hero Banner</span>
</a>

    <a href="{{ route('admin.faqs.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.faqs.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.faqs.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
    </svg>
    <span>FAQ</span>
</a>

{{-- Transaksi Group --}}
<div class="my-3 border-t border-white/10"></div>
<p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-sidebar-text/60">Transaksi</p>

<a href="{{ route('admin.orders.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.orders.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.orders.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
    </svg>
    <span>Pesanan</span>
</a>

<a href="{{ route('admin.reports.orders') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.reports.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.reports.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    <span>Laporan</span>
</a>

<a href="{{ route('admin.product-reviews.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.product-reviews.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.product-reviews.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
    </svg>
    <span>Ulasan Produk</span>
</a>

{{-- Website Group --}}
<div class="my-3 border-t border-white/10"></div>
<p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-sidebar-text/60">Website</p>

<a href="{{ route('admin.settings.index') }}"
    @class([
        'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        'bg-primary text-white shadow-sm' => request()->routeIs('admin.settings.*'),
        'text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover' => !request()->routeIs('admin.settings.*'),
    ])>
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    <span>Pengaturan Website</span>
</a>

{{-- Akun Group --}}
<div class="my-3 border-t border-white/10"></div>
<p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-sidebar-text/60">Akun</p>

<a href="{{ route('profile.edit') }}"
    class="sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover transition-all duration-200">
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
    </svg>
    <span>Profil</span>
</a>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
            class="sidebar-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-300 hover:bg-white/10 transition-all duration-200">
        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        <span>Keluar</span>
    </button>
</form>
