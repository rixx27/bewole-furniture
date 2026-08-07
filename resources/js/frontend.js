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

    // ============================================================
    // categoryShowcase : "Explore Our Collection"
    // Apple-style interactive grid (desktop) + snap scroll (mobile).
    // Tidak ada slider / carousel otomatis.
    // ============================================================
    Alpine.data('categoryShowcase', () => ({
        active: null,
        isMobile: window.matchMedia('(max-width: 767px)').matches,

        init() {
            const mq = window.matchMedia('(max-width: 767px)');
            const onChange = (e) => {
                this.isMobile = e.matches;
            };

            if (typeof mq.addEventListener === 'function') {
                mq.addEventListener('change', onChange);
            } else if (typeof mq.addListener === 'function') {
                mq.addListener(onChange);
            }

            this.$el._categoryShowcaseCleanup = () => {
                if (typeof mq.removeEventListener === 'function') {
                    mq.removeEventListener('change', onChange);
                } else if (typeof mq.removeListener === 'function') {
                    mq.removeListener(onChange);
                }
            };
        },

        destroy() {
            if (typeof this.$el._categoryShowcaseCleanup === 'function') {
                this.$el._categoryShowcaseCleanup();
            }
        },

        /**
         * Desktop/tablet: flex-grow wrapper.
         * Kartu hover membesar (grow 1.4), kartu lain menyusut (grow 0.7).
         * Saat tidak ada hover, semua kartu sama besar (grow 1).
         */
        wrapperStyle(index) {
            if (this.active === null) {
                return 'flex-grow: 1;';
            }

            if (this.active === index) {
                return 'flex-grow: 1.4;';
            }

            return 'flex-grow: 0.72;';
        },

        /**
         * Desktop/tablet: kelas kartu (elemen <a>) berdasarkan state aktif.
         * Kartu non-aktif sedikit meredup & mengecil namun tetap terlihat.
         */
        cardClasses(index) {
            const classes = [
                'transition-all',
                'duration-500',
                'ease-in-out',
            ];

            if (this.active === null) {
                return classes.join(' ');
            }

            if (this.active === index) {
                classes.push('shadow-2xl');
                classes.push('shadow-black/30');
                classes.push('z-10');
                classes.push('scale-[1.02]');
            } else {
                classes.push('opacity-75');
                classes.push('scale-[0.98]');
            }

            return classes.join(' ');
        },

        /**
         * Mobile: deteksi posisi scroll & tentukan kartu aktif
         * agar kartu tengah tampil sedikit lebih besar.
         */
        onMobileScroll(scroller) {
            if (!scroller) return;

            const cards = Array.from(scroller.children);
            const center = scroller.scrollLeft + scroller.clientWidth / 2;

            cards.forEach((card) => {
                const rect = card.getBoundingClientRect();
                const cardCenter = rect.left + rect.width / 2;
                const distance = Math.abs(cardCenter - (scroller.getBoundingClientRect().left + scroller.clientWidth / 2));
                const isActive = distance < rect.width * 0.45;

                card.classList.toggle('category-card-active', isActive);

                const img = card.querySelector('img');
                if (img) {
                    img.classList.toggle('scale-105', isActive);
                }
            });
        },

        /**
         * Mobile: scroll halus menuju kartu yang diklik (snap tetap bekerja).
         */
        scrollToCard(scroller, el) {
            if (!scroller || !el) return;

            scroller.scrollTo({
                left: el.offsetLeft - (scroller.clientWidth - el.clientWidth) / 2,
                behavior: 'smooth',
            });
        },
    }));
});

// ============================================================
// Reveal on scroll (IntersectionObserver, tanpa AOS)
// Elemen dengan atribut [data-reveal] muncul fade-up saat masuk viewport.
// Dukungan stagger: [data-reveal][data-reveal-delay="150"] → tambah delay.
// ============================================================
(function initRevealOnScroll() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion) {
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const revealElements = document.querySelectorAll('[data-reveal]');

    if (revealElements.length === 0) return;

    if (!('IntersectionObserver' in window)) {
        revealElements.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const el = entry.target;
            const delay = parseInt(el.dataset.revealDelay || '0', 10);

            if (delay > 0) {
                el.style.transitionDelay = delay + 'ms';
            }

            el.classList.add('is-revealed');
            observer.unobserve(el);
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -60px 0px',
    });

    revealElements.forEach((el) => observer.observe(el));
})();

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
