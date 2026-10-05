<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foto Progres: {{ $history->status_label }} — #{{ $order?->order_code ?? '' }} | Bewole Jepara</title>

    {{-- Favicon Bewole Jepara (Cache-busted) --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}?v=4" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=4">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #09090b;
            color: #f4f4f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: rgba(18, 18, 22, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            position: sticky;
            top: 0;
            z-index: 50;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: contain;
            background: #fff;
            padding: 2px;
        }

        .header-info h1 {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .header-info p {
            font-size: 12px;
            color: #a1a1aa;
            margin-top: 2px;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(217, 119, 6, 0.18);
            color: #f59e0b;
            border: 1px solid rgba(217, 119, 6, 0.4);
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 6px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
        }

        .btn-download {
            background-color: #b45309;
            color: #ffffff;
        }

        .btn-download:hover {
            background-color: #d97706;
        }

        .btn-close {
            background-color: rgba(255, 255, 255, 0.1);
            color: #e4e4e7;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .btn-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            min-height: 0;
        }

        .image-wrapper {
            position: relative;
            max-width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-image {
            max-width: 100%;
            max-height: calc(100vh - 120px);
            width: auto;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.1);
            object-fit: contain;
            display: block;
        }

        footer {
            padding: 10px 20px;
            background: rgba(18, 18, 22, 0.8);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 11px;
            color: #71717a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        footer a {
            color: #f59e0b;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <div class="header-left">
            <img src="{{ \App\Helpers\WebsiteSettings::logoUrl() ?: asset('apple-touch-icon.png') }}" alt="Bewole Logo" class="brand-logo">
            <div class="header-info">
                <h1>
                    Foto Progres: {{ $history->status_label }}
                    @if($order)
                        <span class="badge-status">#{{ $order->order_code }}</span>
                    @endif
                </h1>
                <p>
                    {{ $history->created_at->translatedFormat('d F Y, H:i') }} WIB
                    @if($history->latitude && $history->longitude)
                        • 📍 GPS: {{ $history->latitude }}, {{ $history->longitude }}
                    @endif
                </p>
            </div>
        </div>

        <div class="header-actions">
            @if($history->latitude && $history->longitude)
                <a href="https://www.google.com/maps?q={{ $history->latitude }},{{ $history->longitude }}"
                   target="_blank"
                   class="btn btn-close"
                   title="Buka Lokasi di Google Maps">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Lokasi Maps</span>
                </a>
            @endif

            <a href="{{ $history->download_url }}" class="btn btn-download">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh Foto</span>
            </a>

            <button type="button" onclick="if(window.history.length > 1){ window.history.back(); } else { window.close(); }" class="btn btn-close">
                Tutup
            </button>
        </div>
    </header>

    <main>
        <div class="image-wrapper">
            <img src="{{ $history->photo_url }}" alt="Foto Progres Pesanan" class="preview-image">
        </div>
    </main>

    <footer>
        <span>Dokumentasi Resmi &copy; {{ date('Y') }} Bewole Jepara Furniture</span>
        @if($order)
            <span>Pelanggan: <strong style="color: #d4d4d8;">{{ $order->customer_name }}</strong></span>
        @endif
    </footer>

</body>
</html>
