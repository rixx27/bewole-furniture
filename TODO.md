# TODO — Phase 2: Hero Section (Bewole Furniture)

## Steps
- [x] 1. Buat `app/Http/Controllers/Frontend/HomeController.php` (ambil hero aktif dari DB)
- [x] 2. Update `routes/web.php` — home `/` pakai HomeController
- [x] 3. Buat `resources/views/frontend/partials/hero.blade.php` (2 kolom, data dari admin hero)
- [x] 4. Update `resources/css/app.css` — animasi hero (floating, fade-slide, scroll indicator)
- [x] 5. Update `resources/views/frontend/pages/home.blade.php` — include hero + samakan ID section placeholder
- [x] 6. Verifikasi: `npm run build`, `php artisan view:cache`, render test
