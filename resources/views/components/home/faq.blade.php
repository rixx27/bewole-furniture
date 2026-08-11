<section
    id="faq"
    aria-labelledby="faq-heading"
    class="relative overflow-hidden bg-wood-bg py-20 sm:py-24 lg:py-28"
>
    {{-- Ambient background blur accents --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="animate-blob absolute top-1/4 -right-32 h-96 w-96 rounded-full bg-wood-secondary/10 blur-3xl"></div>
        <div class="animate-blob absolute bottom-10 -left-32 h-80 w-80 rounded-full bg-wood-primary/5 blur-3xl" style="animation-delay: 5s;"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($faqs->isNotEmpty())
            {{-- ============================================================
                 EDITORIAL TWO-COLUMN LAYOUT
                 Desktop : Kiri (Header & Deskripsi) | Kanan (Accordion Dynamic)
                 Mobile  : 1 Kolom (Header → Accordion)
                 ============================================================ --}}
            <div
                class="grid gap-12 lg:grid-cols-12 lg:gap-16 items-start"
                x-data="{ 
                    activeAccordion: null, 
                    limit: 5, 
                    totalCount: {{ $faqs->count() }} 
                }"
            >
                
                {{-- LEFT COLUMN: Section Information --}}
                <div class="lg:col-span-5 lg:sticky lg:top-28">
                    {{-- Badge --}}
                    <div data-reveal data-reveal-delay="0">
                        <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-wood-primary/15 bg-wood-primary/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-wood-primary">
                            <span class="text-wood-secondary">✦</span>
                            {{ $badge }}
                        </span>
                    </div>

                    {{-- Main Heading --}}
                    <h2
                        id="faq-heading"
                        data-reveal
                        data-reveal-delay="100"
                        class="font-serif text-3xl font-bold tracking-tight text-wood-text sm:text-4xl lg:text-5xl leading-tight"
                    >
                        {{ $heading }}
                    </h2>

                    {{-- Description --}}
                    <p
                        data-reveal
                        data-reveal-delay="200"
                        class="mt-5 text-base leading-relaxed text-wood-muted sm:text-lg max-w-lg"
                    >
                        {{ $description }}
                    </p>

                    {{-- Decorative Accent Line --}}
                    <div data-reveal data-reveal-delay="300" class="mt-8 hidden lg:block">
                        <div class="h-1 w-16 rounded-full bg-wood-secondary/40"></div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Interactive Accordion List --}}
                <div class="lg:col-span-7">
                    <div class="divide-y divide-wood-border/70 border-y border-wood-border/70">
                        @foreach ($faqs as $index => $faq)
                            <div
                                x-show="{{ $index }} < limit"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                data-reveal
                                data-reveal-delay="{{ min($index, 5) * 75 }}"
                                class="py-5 sm:py-6 transition-colors duration-300"
                            >
                                {{-- Accordion Header / Trigger Button --}}
                                <button
                                    type="button"
                                    @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                    class="group flex w-full items-start justify-between gap-4 text-left font-serif text-lg font-semibold text-wood-text transition-colors duration-200 hover:text-wood-primary focus:outline-none cursor-pointer sm:text-xl"
                                    :aria-expanded="activeAccordion === {{ $faq->id }}"
                                    aria-controls="faq-answer-{{ $faq->id }}"
                                >
                                    <span class="pr-2 leading-snug break-words">
                                        {{ $faq->question }}
                                    </span>

                                    {{-- Interactive Icon (+ / -) --}}
                                    <span
                                        class="ml-2 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-wood-border/80 bg-wood-surface text-wood-primary shadow-xs transition-all duration-300 group-hover:border-wood-primary group-hover:bg-wood-primary group-hover:text-white"
                                        :class="{ 'bg-wood-primary text-white border-wood-primary rotate-180': activeAccordion === {{ $faq->id }} }"
                                    >
                                        <template x-if="activeAccordion === {{ $faq->id }}">
                                            <svg class="h-4 w-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                            </svg>
                                        </template>
                                        <template x-if="activeAccordion !== {{ $faq->id }}">
                                            <svg class="h-4 w-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                        </template>
                                    </span>
                                </button>

                                {{-- Accordion Content / Answer Body --}}
                                <div
                                    id="faq-answer-{{ $faq->id }}"
                                    x-show="activeAccordion === {{ $faq->id }}"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
                                    x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                                    x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
                                    class="overflow-hidden"
                                >
                                    <div class="mt-4 pr-6 sm:pr-10 text-sm leading-relaxed text-wood-muted sm:text-base whitespace-pre-line break-words">
                                        {{ $faq->answer }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- LOAD MORE / LOAD LESS BUTTON (ALPINES.JS) --}}
                    @if ($faqs->count() > 5)
                        <div class="mt-8 flex justify-center lg:justify-start">
                            <button
                                type="button"
                                x-show="limit < totalCount"
                                @click="limit = totalCount"
                                class="group inline-flex items-center gap-2 rounded-full border border-wood-primary/30 bg-wood-surface px-6 py-3 text-sm font-semibold text-wood-primary shadow-xs transition-all duration-300 hover:border-wood-primary hover:bg-wood-primary hover:text-white cursor-pointer"
                            >
                                <span>Tampilkan Semua Pertanyaan ({{ $faqs->count() }})</span>
                                <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                x-show="limit >= totalCount && totalCount > 5"
                                x-cloak
                                @click="limit = 5"
                                class="group inline-flex items-center gap-2 rounded-full border border-wood-border bg-wood-surface px-6 py-3 text-sm font-semibold text-wood-muted shadow-xs transition-all duration-300 hover:border-wood-primary hover:text-wood-primary cursor-pointer"
                            >
                                <span>Tampilkan Lebih Sedikit</span>
                                <svg class="h-4 w-4 transition-transform duration-300 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        @else
            {{-- Empty State Sederhana bila belum ada FAQ aktif --}}
            <div data-reveal class="mx-auto max-w-xl text-center py-12">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-wood-primary/10 text-wood-primary mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 3v1.5M15.75 3v1.5M3 8.25h18M3 15.75h18M3 21h18"/>
                    </svg>
                </span>
                <h3 class="font-serif text-xl font-semibold text-wood-text">FAQ Belum Tersedia</h3>
                <p class="mt-2 text-sm text-wood-muted">
                    Saat ini belum ada pertanyaan umum yang dipublikasikan. Silakan hubungi kami untuk informasi lebih lanjut.
                </p>
            </div>
        @endif
    </div>
</section>
