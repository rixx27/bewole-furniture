<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.662 1.435 5.176L2 22l4.958-1.398A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18c-1.61 0-3.098-.444-4.371-1.213l-.313-.189-2.937.827.838-2.863-.207-.329A7.957 7.957 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Integrasi WhatsApp Bot</h2>
                    <p class="text-xs text-text-muted mt-0.5">Kontrol status koneksi, saklar aktif/nonaktif bot, dan riwayat pesan notifikasi pesanan.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="checkConnection"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-xl border border-border bg-card px-4 py-2.5 text-xs font-semibold text-text-secondary hover:bg-bg-secondary transition-all shadow-xs cursor-pointer disabled:opacity-50"
            >
                <svg wire:loading.remove wire:target="checkConnection" class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <svg wire:loading wire:target="checkConnection" class="h-4 w-4 animate-spin text-emerald-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Cek Ulang Koneksi</span>
            </button>
        </div>
    </div>

    {{-- 3 Kartu Kontrol Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card 1: Saklar Bot ON/OFF --}}
        <div class="rounded-2xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-muted">Status Fitur Notifikasi</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $botEnabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $botEnabled ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                        {{ $botEnabled ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
                <h3 class="text-base font-bold text-text-primary dark:text-black">Kirim Pesan Otomatis</h3>
                <p class="mt-1 text-xs text-text-muted leading-relaxed">
                    {{ $botEnabled 
                        ? 'Bot akan otomatis mengirim pesan WhatsApp ke pelanggan saat status pesanan diperbarui.' 
                        : 'Bot dijeda sementara. Tidak ada pesan WhatsApp yang akan dikirim saat status pesanan diubah.' 
                    }}
                </p>
            </div>

            <div class="mt-5 pt-4 border-t border-border flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Saklar Pengaktifan:</span>
                <button
                    type="button"
                    wire:click="toggleBot"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $botEnabled ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-700' }}"
                >
                    <span class="sr-only">Toggle Bot</span>
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out {{ $botEnabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
            </div>
        </div>

        {{-- Card 2: Status Sesi Gateway WA --}}
        <div class="rounded-2xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-muted">Koneksi Gateway API</span>
                    @php $isConnected = $connectionStatus['connected'] ?? false; @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isConnected ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $isConnected ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $connectionStatus['status_text'] ?? 'Memeriksa...' }}
                    </span>
                </div>
                <h3 class="text-base font-bold text-text-primary dark:text-black">Perangkat WhatsApp</h3>
                <p class="mt-1 text-xs text-text-muted leading-relaxed">
                    {{ $connectionStatus['message'] ?? 'Menghubungkan ke server gateway Hostinger...' }}
                </p>
            </div>

            <div class="mt-5 pt-4 border-t border-border flex items-center justify-between gap-2">
                @if (!empty($connectionStatus['qr_url']))
                    <a
                        href="{{ $connectionStatus['qr_url'] }}"
                        target="_blank"
                        class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1"
                    >
                        <span>Buka Layar Scan QR</span>
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif
                <button
                    type="button"
                    wire:click="checkConnection"
                    class="text-xs font-medium text-text-muted hover:text-text-primary transition-colors cursor-pointer"
                >
                    Segarkan
                </button>
            </div>
        </div>

        {{-- Card 3: Uji Coba Pengiriman Pesan --}}
        <div class="rounded-2xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-muted">Tes Bot Langsung</span>
                    <span class="text-[10px] text-text-muted">Live Testing</span>
                </div>
                <h3 class="text-base font-bold text-text-primary dark:text-black">Kirim Pesan Uji Coba</h3>
                <div class="mt-2 space-y-2">
                    <input
                        type="text"
                        wire:model="testPhone"
                        placeholder="Nomor HP (contoh: 081234567890)"
                        class="w-full rounded-xl border border-border bg-bg-secondary/50 px-3 py-1.5 text-xs text-text-primary focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                    @error('testPhone') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-3 pt-3 border-t border-border flex items-center justify-between">
                <button
                    type="button"
                    wire:click="sendTest"
                    wire:loading.attr="disabled"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition-all shadow-xs cursor-pointer disabled:opacity-50"
                >
                    <svg wire:loading.remove wire:target="sendTest" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <svg wire:loading wire:target="sendTest" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Kirim Pesan Tes</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Banner Jika Gateway Offline --}}
    @if (!($connectionStatus['connected'] ?? false))
        <div class="rounded-2xl border border-amber-300 bg-amber-50/70 p-5 dark:border-amber-900/60 dark:bg-amber-950/30">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-200 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">Perhatian: Sesi WhatsApp Belum Terhubung</h4>
                    <p class="mt-1 text-xs text-amber-800/90 dark:text-amber-300/90 leading-relaxed">
                        Server gateway tidak dapat mengirim notifikasi otomatis sebelum Anda menautkan perangkat WhatsApp. Buka tautan di bawah ini di tab baru, lalu scan QR code menggunakan aplikasi WhatsApp di HP Anda (menu <strong>Perangkat Tertaut / Linked Devices</strong>).
                    </p>
                    <div class="mt-3 flex items-center gap-3">
                        <a
                            href="{{ $connectionStatus['qr_url'] ?? '#' }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-700 px-4 py-2 text-xs font-bold text-white hover:bg-amber-800 transition-colors shadow-xs"
                        >
                            <span>Buka Halaman Scan QR Code</span>
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        <button
                            type="button"
                            wire:click="checkConnection"
                            class="rounded-xl border border-amber-400 bg-white px-3 py-2 text-xs font-semibold text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer"
                        >
                            Saya Sudah Scan, Cek Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabel Riwayat Log Notifikasi WhatsApp --}}
    <div class="rounded-2xl border border-border bg-card shadow-xs overflow-hidden">
        <div class="p-5 border-b border-border flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-bold text-text-primary dark:text-black">Riwayat Notifikasi WhatsApp</h3>
                <p class="text-xs text-text-muted mt-0.5">Daftar pesan notifikasi status pesanan yang dikirimkan ke pelanggan.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari kode pesanan / HP / pesan..."
                        class="w-full sm:w-64 rounded-xl border border-border bg-bg-secondary/50 pl-8 pr-3 py-1.5 text-xs text-text-primary placeholder:text-text-muted focus:border-emerald-500 focus:outline-none"
                    />
                    <svg class="absolute left-2.5 top-2 h-3.5 w-3.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    wire:model.live="statusFilter"
                    class="rounded-xl border border-border bg-bg-secondary/50 px-3 py-1.5 text-xs text-text-primary focus:border-emerald-500 focus:outline-none cursor-pointer"
                >
                    <option value="">Semua Status</option>
                    <option value="sent">Berhasil Terkirim</option>
                    <option value="failed">Gagal Terkirim</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-bg-secondary/60 text-text-muted uppercase font-bold border-b border-border">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Pesanan</th>
                        <th class="px-5 py-3">Penerima</th>
                        <th class="px-5 py-3">Status Trigger</th>
                        <th class="px-5 py-3">Isi Pesan</th>
                        <th class="px-5 py-3">Status Pengiriman</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-bg-secondary/30 transition-colors">
                            <td class="px-5 py-3.5 text-text-muted whitespace-nowrap">
                                <span class="font-medium text-text-primary dark:text-black">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                <span class="block text-[10px] text-text-muted">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-bold">
                                @if ($log->order)
                                    <a href="{{ route('admin.orders.show', $log->order) }}" class="text-emerald-700 dark:text-emerald-400 hover:underline">
                                        #{{ $log->order_code }}
                                    </a>
                                @else
                                    <span class="text-text-muted">#{{ $log->order_code }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <a
                                    href="https://wa.me/{{ $log->recipient_phone }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 font-mono text-emerald-700 dark:text-emerald-400 hover:underline font-semibold"
                                >
                                    <span>{{ $log->recipient_phone }}</span>
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2 py-0.5 rounded-md font-medium text-[11px] bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300">
                                    {{ $log->status_trigger }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <div x-data="{ expanded: false }">
                                    <p class="text-text-secondary leading-relaxed" :class="expanded ? '' : 'line-clamp-2'">
                                        {{ $log->message }}
                                    </p>
                                    @if (strlen($log->message) > 80)
                                        <button
                                            type="button"
                                            @click="expanded = !expanded"
                                            class="text-[10px] font-bold text-emerald-600 hover:underline mt-0.5 cursor-pointer"
                                            x-text="expanded ? 'Tutup Salinan' : 'Lihat Selengkapnya'"
                                        ></button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if ($log->status === 'sent')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Terkirim
                                    </span>
                                @else
                                    <div>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Gagal
                                        </span>
                                        @if ($log->error_message)
                                            <span class="block text-[10px] text-rose-600 dark:text-rose-400 mt-1 max-w-[150px] truncate" title="{{ $log->error_message }}">
                                                {{ $log->error_message }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <button
                                    type="button"
                                    wire:click="resendLog({{ $log->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-2.5 py-1 text-xs font-semibold text-text-secondary hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs"
                                    title="Kirim Ulang Notifikasi"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Kirim Ulang</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-text-muted">
                                <svg class="mx-auto h-8 w-8 text-text-muted/40 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="font-medium text-xs">Belum ada riwayat notifikasi WhatsApp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="p-4 border-t border-border">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
