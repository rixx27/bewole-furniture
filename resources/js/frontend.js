/**
 * Bewole Furniture — Frontend behaviors.
 *
 * - Navbar liquid glass: solid state saat scroll
 * - Smooth scroll untuk anchor link
 * - Back to top (didukung juga di layout via Alpine)
 */

import Alpine from 'alpinejs';

// Daftarkan komponen navbar
document.addEventListener('alpine:init', () => {
    Alpine.data('navbar', () => ({
        scrolled: false,
        mobileOpen: false,

        init() {
            const onScroll = () => {
                this.scrolled = window.scrollY > 20;
            };

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        },
    }));
});

// ============================================================
// Smooth Scroll (semua anchor menuju section dalam halaman)
// ============================================================
document.addEventListener('click', function (e) {
    const link = e.target.closest('a[href^="#"]');

    if (!link) return;

    const id = link.getAttribute('href');

    if (id.length <= 1) return;

    const target = document.querySelector(id);

    if (!target) return;

    e.preventDefault();

    const headerOffset = 88;
    const elementPosition = target.getBoundingClientRect().top + window.scrollY;
    const offsetPosition = elementPosition - headerOffset;

    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth',
    });

    // Update hash tanpa jump
    history.replaceState(null, '', id);
});

// ============================================================
// Tutup menu mobile saat klik di luar
// ============================================================
document.addEventListener('click', function (e) {
    const header = document.querySelector('header[x-data="navbar"]');

    if (!header) return;

if (!header.contains(e.target)) {
        const data = header.__x?.$data;
        if (data) {
            data.mobileOpen = false;
        }
    }
});

// Jalankan Alpine
window.Alpine = Alpine;
Alpine.start();
