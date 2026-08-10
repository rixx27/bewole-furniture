# TODO: Perbaiki Navigasi/Button Hero Banner

## Steps
- [x] Analisis kode & buat rencana
- [x] 1. Tambah `resolveHref()` dinamis di `app/Enums/HeroButtonTarget.php`
- [x] 2. Pakai `resolveHref()` di `resources/views/frontend/partials/hero.blade.php`
- [x] 3. Pakai `resolveHref()` di `resources/views/components/hero-button.blade.php`
- [x] 4. Ubah `id="philosophy"` → `id="about"` di `components/home/philosophy.blade.php`
- [x] 5. Perbaiki `frontend/pages/home.blade.php` (hapus div about ganda, tambah scroll-mt)
- [x] 6. Perbaiki hash navbar (`#produk`→`#products`, `#kontak`→`#contact`)
- [x] 7. Tambah smooth scroll + scroll-margin-top di `resources/css/app.css`
- [x] 8. Perbaiki offset smooth scroll di `resources/js/frontend.js`
- [x] 9. Verifikasi final (resolveHref diuji: anchor, path, route, URL, null)
