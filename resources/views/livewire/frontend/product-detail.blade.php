<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

    <div class="grid gap-12 lg:grid-cols-12">
        {{-- Gallery (6 cols) --}}
        <div class="lg:col-span-6 space-y-4">
            {{-- Main Active Image --}}
            <div class="relative aspect-[4/3] w-full overflow-hidden rounded-3xl border border-wood-border/60 bg-wood-light/40 shadow-sm">
                @if ($selectedImage)
                    <img
                        src="{{ asset('storage/' . $selectedImage) }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover transition-all duration-300"
                    />
                @else
                    <div class="flex h-full w-full items-center justify-center text-wood-muted">
                        <svg class="h-16 w-16 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif

                @if ($product->has_discount)
                    <div class="absolute right-4 top-4">
                        <span class="rounded-full bg-amber-600 px-3 py-1 text-xs font-bold text-white shadow-md">
                            DISKON {{ $product->discount_percentage }}%
                        </span>
                    </div>
                @endif
            </div>

            {{-- Thumbnail Grid --}}
            @if ($product->images->count() > 0 || $product->thumbnail)
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
                    @if ($product->thumbnail)
                        <button
                            type="button"
                            wire:click="selectImage('{{ $product->thumbnail }}')"
                            class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border-2 transition-all {{ $selectedImage === $product->thumbnail ? 'border-wood-primary ring-2 ring-wood-primary/20' : 'border-wood-border/60 opacity-70 hover:opacity-100' }}"
                        >
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="Thumbnail" class="h-full w-full object-cover">
                        </button>
                    @endif

                    @foreach ($product->images as $img)
                        <button
                            type="button"
                            wire:click="selectImage('{{ $img->image }}')"
                            class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border-2 transition-all {{ $selectedImage === $img->image ? 'border-wood-primary ring-2 ring-wood-primary/20' : 'border-wood-border/60 opacity-70 hover:opacity-100' }}"
                        >
                            <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Details Info (6 cols) --}}
        <div class="lg:col-span-6 flex flex-col">
            {{-- Category & Status --}}
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-wood-primary/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-wood-primary">
                    {{ $product->category?->name ?? 'Bewole Furniture' }}
                </span>
                <span class="rounded-full px-3.5 py-1 text-xs font-semibold {{ $product->status === 'sold_out' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                    {{ $product->status_label }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-wood-text font-serif lg:text-4xl">
                {{ $product->name }}
            </h1>

            {{-- Price --}}
            <div class="mt-4 flex items-baseline gap-3">
                <span class="text-2xl font-bold text-wood-primary lg:text-3xl">
                    {{ $product->formatted_discount_price ?: $product->formatted_price }}
                </span>
                @if ($product->has_discount)
                    <span class="text-base text-wood-muted line-through">
                        {{ $product->formatted_price }}
                    </span>
                @endif
            </div>

            {{-- Short Description --}}
            @if ($product->short_description)
                <p class="mt-4 text-sm text-wood-muted leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- Specifications Table --}}
            <div class="mt-6 rounded-2xl border border-wood-border/50 bg-wood-light/20 p-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-wood-text border-b border-wood-border/40 pb-2 mb-3">Spesifikasi Produk</h3>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    @if ($product->sku)
                        <div>
                            <span class="text-wood-muted block">SKU / Kode</span>
                            <span class="font-medium text-wood-text font-mono">{{ $product->sku }}</span>
                        </div>
                    @endif
                    @if ($product->material)
                        <div>
                            <span class="text-wood-muted block">Material</span>
                            <span class="font-medium text-wood-text">{{ $product->material }}</span>
                        </div>
                    @endif
                    @if ($product->dimensions)
                        <div>
                            <span class="text-wood-muted block">Dimensi (PxLxT)</span>
                            <span class="font-medium text-wood-text">{{ $product->dimensions }}</span>
                        </div>
                    @endif
                    @if ($product->weight)
                        <div>
                            <span class="text-wood-muted block">Estimasi Berat</span>
                            <span class="font-medium text-wood-text">{{ $product->weight }} kg</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-wood-muted block">Ketersediaan Stok</span>
                        <span class="font-medium text-wood-text">{{ $product->stock > 0 ? $product->stock . ' unit' : 'Stok Kosong / Pre-Order' }}</span>
                    </div>
                </div>
            </div>

            {{-- Quantity & Action Controls --}}
            <div class="mt-8 space-y-4">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-wood-text">Jumlah:</span>
                    <div class="flex items-center rounded-full border border-wood-border/60 bg-white p-1 shadow-sm">
                        <button
                            type="button"
                            wire:click="decrementQuantity"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-wood-text hover:bg-wood-light transition-all"
                        >
                            -
                        </button>
                        <span class="w-10 text-center text-xs font-bold text-wood-text">{{ $quantity }}</span>
                        <button
                            type="button"
                            wire:click="incrementQuantity"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-wood-text hover:bg-wood-light transition-all"
                        >
                            +
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <button
                        type="button"
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        wire:target="addToCart"
                        class="rounded-2xl border-2 border-wood-primary bg-white py-3.5 text-center text-xs font-bold uppercase tracking-wider text-wood-primary transition-all hover:bg-wood-primary/5 hover:shadow-md flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg wire:loading.remove wire:target="addToCart" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <svg wire:loading wire:target="addToCart" class="h-4 w-4 animate-spin text-wood-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Tambah ke Keranjang</span>
                    </button>

                    <button
                        type="button"
                        wire:click="buyNow"
                        wire:loading.attr="disabled"
                        wire:target="buyNow"
                        class="rounded-2xl bg-wood-primary py-3.5 text-center text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-wood-primary/25 transition-all hover:bg-wood-primary-dark hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg wire:loading wire:target="buyNow" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Beli Sekarang</span>
                    </button>
                </div>
            </div>

            {{-- Full Description --}}
            @if ($product->description)
                <div class="mt-10 border-t border-wood-border/40 pt-6">
                    <h3 class="text-base font-bold text-wood-text font-serif mb-3">Deskripsi Lengkap</h3>
                    <div class="prose prose-sm text-wood-muted max-w-none leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         PRODUCT REVIEWS & RATINGS SECTION
         ============================================================ --}}
    <div id="ulasan" class="mt-20 border-t border-wood-border/60 pt-16">
        @php
            $visibleReviews = $product->visibleReviews;
            $reviewsCount = $visibleReviews->count();
            $avgRating = $reviewsCount > 0 ? round($visibleReviews->avg('rating'), 1) : 0;
            $starCounts = [
                5 => $visibleReviews->where('rating', 5)->count(),
                4 => $visibleReviews->where('rating', 4)->count(),
                3 => $visibleReviews->where('rating', 3)->count(),
                2 => $visibleReviews->where('rating', 2)->count(),
                1 => $visibleReviews->where('rating', 1)->count(),
            ];
        @endphp

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-wood-primary/20 bg-wood-primary/5 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-wood-primary">
                    <span class="text-amber-500">★</span> Ulasan Pelanggan
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-serif font-bold text-wood-text">
                    Penilaian & Ulasan Produk
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-wood-muted">
                    Ulasan otentik dari pembeli terverifikasi Bewole Furniture.
                </p>
            </div>
        </div>

        {{-- Review Summary & Statistics --}}
        <div class="grid gap-8 lg:grid-cols-12 mb-12">
            {{-- Overall Score (4 cols) --}}
            <div class="lg:col-span-4 rounded-3xl border border-wood-border/60 bg-white p-6 sm:p-8 shadow-sm flex flex-col justify-center items-center text-center">
                <div class="text-5xl font-extrabold text-wood-text font-serif">
                    {{ $avgRating > 0 ? number_format($avgRating, 1) : '0.0' }}
                </div>
                <div class="flex items-center gap-1 mt-3">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="h-5 w-5 {{ $i <= round($avgRating) && $reviewsCount > 0 ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="mt-2 text-xs font-semibold text-wood-muted">
                    Berdasarkan {{ $reviewsCount }} {{ Str::plural('ulasan', $reviewsCount) }}
                </p>
            </div>

            {{-- Breakdown Bars (8 cols) --}}
            <div class="lg:col-span-8 rounded-3xl border border-wood-border/60 bg-white p-6 sm:p-8 shadow-sm flex flex-col justify-center space-y-2.5">
                @foreach ([5, 4, 3, 2, 1] as $star)
                    @php
                        $count = $starCounts[$star];
                        $pct = $reviewsCount > 0 ? round(($count / $reviewsCount) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3 text-xs">
                        <span class="w-12 font-medium text-wood-text flex items-center gap-1">
                            {{ $star }} <span class="text-amber-500">★</span>
                        </span>
                        <div class="flex-1 h-2.5 rounded-full bg-wood-light/60 overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-12 text-right font-mono text-wood-muted text-[11px]">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Review Form & Eligibility Notices --}}
        <div class="mb-14">
            @if (session()->has('review_success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-sm text-emerald-800 flex items-start gap-3 shadow-sm">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold">Ulasan Berhasil Dikirim!</p>
                        <p class="text-xs text-emerald-700 mt-0.5">{{ session('review_success') }}</p>
                    </div>
                </div>
            @endif

            @if (session()->has('review_error'))
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-5 text-sm text-rose-800 flex items-start gap-3 shadow-sm">
                    <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold">Tidak Dapat Mengirim Ulasan</p>
                        <p class="text-xs text-rose-700 mt-0.5">{{ session('review_error') }}</p>
                    </div>
                </div>
            @endif

            @if ($this->canReview)
                {{-- Review Submission Form --}}
                <div class="rounded-3xl border-2 border-wood-primary/30 bg-white p-6 sm:p-8 shadow-md">
                    <div class="flex items-center gap-3 border-b border-wood-border/40 pb-4 mb-6">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-wood-text font-serif">Tulis Ulasan Anda</h3>
                            <p class="text-xs text-wood-muted">Bagikan pengalaman Anda tentang kualitas dan kenyamanan produk ini.</p>
                        </div>
                    </div>

                    <form wire:submit="submitReview" class="space-y-6">
                        {{-- Rating Selection --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                Berikan Penilaian Bintang <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('rating', {{ $i }})"
                                        class="group p-1 transition-transform hover:scale-125 focus:outline-none"
                                    >
                                        <svg class="h-8 w-8 {{ $i <= $rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200 group-hover:text-amber-200 group-hover:fill-amber-200' }} transition-colors" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                                <span class="ml-3 text-xs font-bold text-wood-primary">
                                    {{ match($rating) {
                                        1 => '1/5 — Sangat Buruk',
                                        2 => '2/5 — Buruk',
                                        3 => '3/5 — Cukup',
                                        4 => '4/5 — Baik',
                                        5 => '5/5 — Sangat Baik',
                                        default => ''
                                    } }}
                                </span>
                            </div>
                            @error('rating')
                                <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Comment --}}
                        <div>
                            <label for="comment" class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                Ulasan / Komentar Anda
                            </label>
                            <textarea
                                id="comment"
                                wire:model="comment"
                                rows="4"
                                placeholder="Bagaimana kesan Anda terhadap material kayu, kerapian finishing, kecocokan ukuran, dan packaging produk?"
                                class="w-full rounded-2xl border border-wood-border/60 bg-wood-light/20 p-4 text-xs sm:text-sm text-wood-text placeholder:text-wood-muted/70 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20 transition-all"
                            ></textarea>
                            @error('comment')
                                <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Photos Upload --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-wood-text mb-2">
                                Foto Produk (Opsional, Maksimal 5 Foto)
                            </label>

                            <label for="photos" class="mt-1 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-wood-border/80 px-6 pt-5 pb-6 bg-wood-light/10 hover:bg-wood-light/25 hover:border-wood-primary/60 transition-all cursor-pointer group">
                                <div class="space-y-1.5 text-center">
                                    <svg class="mx-auto h-10 w-10 text-wood-muted group-hover:text-wood-primary transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-xs text-wood-muted justify-center items-center">
                                        <span class="font-bold text-wood-primary group-hover:text-wood-primary-dark group-hover:underline transition-colors">
                                            Pilih Foto
                                        </span>
                                        <span class="pl-1">atau tarik ke sini</span>
                                    </div>
                                    <p class="text-[10px] text-wood-muted">Format PNG, JPG, JPEG, WEBP hingga 2MB per file (maks. 5 foto)</p>
                                </div>
                                <input id="photos" type="file" wire:model="photos" multiple accept="image/png, image/jpeg, image/jpg, image/webp" class="sr-only">
                            </label>

                            <div wire:loading wire:target="photos" class="mt-2 text-xs text-wood-primary font-medium flex items-center gap-2">
                                <svg class="animate-spin h-3.5 w-3.5 text-wood-primary" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Sedang mengunggah foto...
                            </div>

                            @error('photos')
                                <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror

                            {{-- Preview Photos Grid --}}
                            @if (!empty($photos))
                                <div class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3">
                                    @foreach ($photos as $index => $photo)
                                        <div class="relative aspect-square rounded-xl overflow-hidden border border-wood-border/60 group bg-wood-light/40">
                                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover">
                                            <button
                                                type="button"
                                                wire:click="removePhoto({{ $index }})"
                                                class="absolute top-1 right-1 h-6 w-6 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs opacity-90 hover:opacity-100 shadow-md transition-opacity"
                                                title="Hapus Foto"
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end pt-2">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="rounded-2xl bg-wood-primary px-8 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-wood-primary/25 transition-all hover:bg-wood-primary-dark hover:-translate-y-0.5 disabled:opacity-50 flex items-center gap-2"
                            >
                                <span wire:loading.remove wire:target="submitReview">Kirim Ulasan</span>
                                <span wire:loading wire:target="submitReview" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @elseif ($this->hasReviewed)
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50/60 p-6 sm:p-8 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-900">Terima Kasih Atas Ulasan Anda!</h3>
                        <p class="mt-0.5 text-xs text-emerald-700">
                            Anda telah memberikan ulasan untuk produk ini. Ulasan Anda sangat berharga bagi pengrajin dan pelanggan Bewole Furniture.
                        </p>
                    </div>
                </div>
            @elseif (auth()->check())
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 sm:p-8 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-wood-text">Ingin Memberikan Ulasan?</h3>
                        <p class="mt-0.5 text-xs text-wood-muted">
                            Hanya pelanggan yang telah membeli produk ini dan status pesanannya telah selesai yang dapat memberikan penilaian & ulasan.
                        </p>
                    </div>
                </div>
            @else
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-wood-text">Punya Produk Ini?</h3>
                            <p class="mt-0.5 text-xs text-wood-muted">Masuk ke akun Anda untuk memberikan ulasan setelah menyelesaikan pembelian.</p>
                        </div>
                    </div>
                    <a
                        href="{{ route('login') }}"
                        class="shrink-0 rounded-2xl border-2 border-wood-primary bg-white px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-wood-primary hover:bg-wood-primary hover:text-white transition-all shadow-sm"
                    >
                        Masuk / Login
                    </a>
                </div>
            @endif
        </div>

        {{-- Visible Reviews List --}}
        <div class="space-y-6">
            <h3 class="text-base font-bold text-wood-text font-serif border-b border-wood-border/40 pb-3">
                Daftar Ulasan ({{ $visibleReviews->count() }})
            </h3>

            @forelse ($visibleReviews as $review)
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 sm:p-8 shadow-sm">
                    {{-- Reviewer Info & Rating Stars --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-wood-border/40 pb-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-wood-primary/10 text-xs font-bold text-wood-primary">
                                {{ $review->user?->initials() ?? 'U' }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold text-wood-text">{{ $review->user?->name ?? 'Pelanggan Bewole' }}</h4>
                                    @if ($review->is_verified)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-200">
                                            ✓ Pembeli Terverifikasi
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-wood-muted">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-xs font-semibold text-wood-text">({{ $review->rating_label }})</span>
                        </div>
                    </div>

                    {{-- Review Comment --}}
                    @if ($review->comment)
                        <p class="text-xs sm:text-sm text-wood-text/90 leading-relaxed">
                            {{ $review->comment }}
                        </p>
                    @endif

                    {{-- Review Photos --}}
                    @if ($review->images->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-2.5">
                            @foreach ($review->images as $img)
                                <a
                                    href="{{ asset('storage/' . $img->image) }}"
                                    target="_blank"
                                    class="group relative h-20 w-20 sm:h-24 sm:w-24 overflow-hidden rounded-2xl border border-wood-border/60 bg-wood-light/30 shadow-xs"
                                >
                                    <img src="{{ asset('storage/' . $img->image) }}" alt="Foto ulasan" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl border border-wood-border/60 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-wood-light/60 text-wood-muted mb-3">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-wood-text">Belum Ada Ulasan</h4>
                    <p class="mt-1 text-xs text-wood-muted">Jadilah yang pertama membagikan ulasan untuk produk ini!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

