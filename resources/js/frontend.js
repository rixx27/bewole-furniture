/**
 * Bewole Furniture — Frontend behaviors.
 *
 * - Navbar liquid glass: solid state saat scroll
 * - Smooth scroll untuk anchor link
 * - Back to top (didukung juga di layout via Alpine)
 */

// Register Alpine components safely with Livewire
function initBewoleAlpine(AlpineInstance) {
    if (!AlpineInstance || AlpineInstance._bewoleRegistered) return;
    AlpineInstance._bewoleRegistered = true;

    AlpineInstance.data('navbar', () => ({
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

    AlpineInstance.data('categoryShowcase', () => ({
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

        wrapperStyle(index) {
            if (this.active === null) {
                return 'flex-grow: 1;';
            }

            if (this.active === index) {
                return 'flex-grow: 1.4;';
            }

            return 'flex-grow: 0.72;';
        },

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

        scrollToCard(scroller, el) {
            if (!scroller || !el) return;

            scroller.scrollTo({
                left: el.offsetLeft - (scroller.clientWidth - el.clientWidth) / 2,
                behavior: 'smooth',
            });
        },
    }));
}

document.addEventListener('alpine:init', () => {
    initBewoleAlpine(window.Alpine);
});

if (window.Alpine) {
    initBewoleAlpine(window.Alpine);
}

// ============================================================
// Reveal on scroll (IntersectionObserver, tanpa AOS)
// ============================================================
(function initRevealOnScroll() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealSelector = '[data-reveal], [data-reveal-side], [data-reveal-blur]';

    if (prefersReducedMotion) {
        document.querySelectorAll(revealSelector).forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const revealElements = document.querySelectorAll(revealSelector);
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

    const computed = window.getComputedStyle(target);
    const cssOffset = parseFloat(computed.scrollMarginTop) || 0;
    const headerOffset = cssOffset > 0 ? cssOffset : 88;
    const elementPosition = target.getBoundingClientRect().top + window.scrollY;
    const offsetPosition = elementPosition - headerOffset;

    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth',
    });

    history.replaceState(null, '', id);
});
