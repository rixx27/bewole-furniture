<div
    x-data="{
        open: false,
        searchQuery: '',
        results: [],
        loading: false,
        debounceTimer: null,
        searchUrl: '{{ url('/search') }}',
        suggestUrl: '{{ url('/api/search/suggest') }}',

        openSearch() {
            this.open = true;
            this.$nextTick(() => this.$refs.searchInput.focus());
        },

        closeSearch() {
            this.open = false;
        },

        clearSearch() {
            this.searchQuery = '';
            this.results = [];
            this.$refs.searchInput.focus();
        },

        submitSearch() {
            const q = this.searchQuery.trim();
            if (q.length > 0) {
                window.location.href = this.searchUrl + '?q=' + encodeURIComponent(q);
            }
        },

        fetchSuggestions() {
            clearTimeout(this.debounceTimer);
            const q = this.searchQuery.trim();

            if (q.length < 2) {
                this.results = [];
                this.loading = false;
                return;
            }

            this.loading = true;
            this.debounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(this.suggestUrl + '?q=' + encodeURIComponent(q), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (response.ok) {
                        this.results = await response.json();
                    }
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            }, 300);
        }
    }"
    class="relative flex items-center"
    @keydown.escape.window="closeSearch()"
>
    {{-- Search Icon Button --}}
    <button
        type="button"
        @click="openSearch()"
        class="relative flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
        :class="scrolled ? 'text-wood-text hover:bg-wood-primary/10 hover:text-wood-primary' : 'text-white hover:bg-white/15'"
        title="Cari Produk"
        aria-label="Cari Produk"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </button>

    {{-- Dropdown Search Interface --}}
    <div
        x-show="open"
        x-cloak
        @click.away="closeSearch()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-full mt-3 w-[calc(100vw-2rem)] max-w-md origin-top-right rounded-3xl border border-wood-border/60 bg-white shadow-2xl shadow-wood-primary/10 sm:w-96"
    >
        <div class="p-3">
            <form @submit.prevent="submitSearch()" class="relative">
                <input
                    x-ref="searchInput"
                    type="text"
                    x-model="searchQuery"
                    @input="fetchSuggestions()"
                    placeholder="Cari kursi, lemari, meja jati..."
                    class="w-full rounded-2xl border border-wood-border/50 bg-wood-light/10 py-3 pl-11 pr-10 text-sm font-medium text-wood-text placeholder-wood-muted shadow-inner focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                >
                <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-wood-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <template x-if="loading">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="h-4 w-4 animate-spin text-wood-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </template>
                <template x-if="!loading && searchQuery.length > 0">
                    <button type="button" @click="clearSearch()" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-wood-muted hover:bg-wood-light/50 hover:text-wood-text">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </template>
            </form>

            {{-- Results Area --}}
            <div class="mt-3">

                {{-- Has Results --}}
                <template x-if="searchQuery.trim().length >= 2 && results.length > 0">
                    <div>
                        <div class="space-y-1 max-h-80 overflow-y-auto pr-1">
                            <template x-for="item in results" :key="item.slug">
                                <a :href="item.url" class="group flex items-center gap-4 rounded-2xl p-2 transition-colors hover:bg-wood-light/20">
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-wood-light/40 border border-wood-border/40">
                                        <template x-if="item.thumbnail">
                                            <img :src="'/storage/' + item.thumbnail" :alt="item.name" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!item.thumbnail">
                                            <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                                <svg class="h-5 w-5 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-wood-primary truncate" x-text="item.category_name"></span>
                                        <h4 class="text-xs font-semibold text-wood-text truncate group-hover:text-wood-primary" x-text="item.name"></h4>
                                        <p class="text-xs text-wood-muted" x-text="item.price"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                        <div class="mt-2 border-t border-wood-border/40 pt-2">
                            <a :href="searchUrl + '?q=' + encodeURIComponent(searchQuery.trim())" class="block w-full rounded-xl p-2 text-center text-xs font-semibold text-wood-primary hover:bg-wood-primary/10 transition-colors">
                                Lihat semua hasil →
                            </a>
                        </div>
                    </div>
                </template>

                {{-- No Results --}}
                <template x-if="!loading && searchQuery.trim().length >= 2 && results.length === 0">
                    <div class="py-8 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-wood-light/50 text-wood-muted mb-3">
                            <svg class="h-6 w-6 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-wood-text">Produk tidak ditemukan</p>
                        <p class="text-xs text-wood-muted mt-1">Maaf, tidak ada produk yang sesuai dengan "<span x-text="searchQuery"></span>".</p>
                        <a href="{{ route('products.index') }}" class="mt-3 inline-block rounded-full bg-wood-primary/10 px-4 py-2 text-xs font-semibold text-wood-primary hover:bg-wood-primary hover:text-white transition-colors">
                            Lihat Semua Produk
                        </a>
                    </div>
                </template>

                {{-- 1 char typed --}}
                <template x-if="searchQuery.trim().length === 1">
                    <div class="py-4 text-center">
                        <p class="text-xs text-wood-muted">Ketik minimal 2 karakter untuk mencari...</p>
                    </div>
                </template>

                {{-- Default / Empty State --}}
                <template x-if="searchQuery.trim().length === 0">
                    <div class="py-2 px-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-wood-muted mb-2">Produk Populer</h4>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="searchQuery = 'kursi'; fetchSuggestions();" class="rounded-full border border-wood-border/60 px-3 py-1.5 text-[11px] font-medium text-wood-text hover:border-wood-primary hover:text-wood-primary transition-colors">Kursi</button>
                            <button type="button" @click="searchQuery = 'meja'; fetchSuggestions();" class="rounded-full border border-wood-border/60 px-3 py-1.5 text-[11px] font-medium text-wood-text hover:border-wood-primary hover:text-wood-primary transition-colors">Meja</button>
                            <button type="button" @click="searchQuery = 'lemari'; fetchSuggestions();" class="rounded-full border border-wood-border/60 px-3 py-1.5 text-[11px] font-medium text-wood-text hover:border-wood-primary hover:text-wood-primary transition-colors">Lemari</button>
                            <button type="button" @click="searchQuery = 'jati'; fetchSuggestions();" class="rounded-full border border-wood-border/60 px-3 py-1.5 text-[11px] font-medium text-wood-text hover:border-wood-primary hover:text-wood-primary transition-colors">Kayu Jati</button>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>
