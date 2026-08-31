# LAPORAN AUDIT MENYELURUH — PROJECT BEWOLE FURNITURE

Audit dilakukan secara komprehensif oleh Senior Laravel Full-Stack Developer, Software Architect, QA Engineer, dan Debugging Specialist melalui penelusuran menyeluruh dari:
$$\text{Frontend} \longrightarrow \text{Route} \longrightarrow \text{Controller} \longrightarrow \text{Validation} \longrightarrow \text{Model} \longrightarrow \text{Database} \longrightarrow \text{Response} \longrightarrow \text{Blade}$$

---

## 1. Executive Summary

Berdasarkan audit statis, penelusuran arsitektur kode, skema database aktual (MySQL), route list, serta integritas view, ditemukan beberapa temuan kritis yang memblokir fungsionalitas utama (*blockers*):

1. **Order & Checkout Crashing:** Migration penambahan kolom customisasi/packing dan tabel `order_items` berstatus **PENDING** dengan timestamp duplikat. Akibatnya, alur checkout gagal di level database (*Unknown column / Table doesn't exist*).
2. **Missing Route Views (500 Error):** Route public `/portfolio`, `/testimonials`, `/faq`, `/contact`, serta route user `/profile` mengarah ke file Blade yang **TIDAK DITEMUKAN** di `resources/views/frontend/`.
3. **Missing Regions Dataset for Checkout Deployment:** File `storage/app/indonesia_regions.json` diabaikan (*ignored*) oleh Git karena konfigurasi wildcard di `storage/app/.gitignore`. Pada instalasi/deployment baru, dropdown provinsi/kota akan kosong total dan checkout gagal validasi.
4. **Alur Review Frontend Belum Lengkap:** Backend Admin Review moderasi sudah ada, tetapi belum ada form submit review maupun rendering daftar review di halaman detail produk pada sisi user.
5. **Inkonsistensi Status & View Bugs:** Ditemukan kesalahan nama property (`$history->notes` vs `$history->description`) pada riwayat pesanan serta status badge lama yang menyebabkan status valid tampil dengan styling fallback abu-abu.

---

## 2. Project Health Score

```text
╔═══════════════════════════════════════════════════════════════════╗
║                   OVERALL HEALTH SCORE: 68 / 100                  ║
╚═══════════════════════════════════════════════════════════════════╝
```

### Breakdown Penilaian:
* **Functionality & Stability (60/100):** Alur katalog, cart, dan autentikasi dasar berfungsi, namun checkout terblokir skema DB pending, review user belum terhubung, dan 5 route menghasilkan 500 View Not Found.
* **Database & Migration Consistency (65/100):** Sebagian besar tabel sinkron, tetapi ada migration pending dengan timestamp ganda dan tabel `order_items` belum terbentuk di MySQL.
* **Authentication & Authorization (88/100):** Implementasi Fortify, Spatie Permission, Google OAuth controller, dan User/Admin separation dirancang dengan baik dan terlindungi middleware `role:admin`.
* **Security & Data Integrity (85/100):** Proteksi CSRF aktif, IDOR dicegah pada endpoint order user, perhitungan harga dilakukan server-side (anti-price-tampering).
* **UI/UX & Design System (75/100):** Desain premium bernuansa *furniture luxury* (palet kayu `#5B3A29`, `#A67C52`, `#F7F4EF`), animasi Alpine.js, dan layout Tailwind v4 berjalan baik, namun terdapat inkonsistensi badge status dan beberapa view hilang.
* **Deployment Readiness (45/100):** File dataset wilayah ter-ignore oleh Git, kredensial OAuth & Mailer log masih konfigurasi lokal, unbound version constraints di composer.

---

## 3. Critical Errors

### ERR-001 — Skema Database Tidak Sinkron: Migration Pending & Tabel `order_items` Belum Ada

* **Category:** Database / Core Function
* **Severity:** CRITICAL
* **Status:** VERIFIED
* **Feature:** Checkout & Order Processing

**Location:**
* [2026_08_27_000000_add_customization_and_packing_fields.php](file:///c:/xampp/htdocs/bewole-furniture/database/migrations/2026_08_27_000000_add_customization_and_packing_fields.php)
* [OrderService.php](file:///c:/xampp/htdocs/bewole-furniture/app/Services/OrderService.php#L49)
* [Order.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/Order.php#L20-L49)
* [OrderItem.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/OrderItem.php)

**Symptom:**
Ketika user melakukan proses checkout dan menekan tombol *Beli/Pesan*, eksekusi database menghasilkan fatal exception:
`SQLSTATE[42S22]: Column not found: 1054 Unknown column 'meubel_type' in 'field list'`
dan/atau
`SQLSTATE[42S02]: Base table or view not found: 1146 Table 'furniture.order_items' doesn't exist`

**Root Cause:**
Migration [2026_08_27_000000_add_customization_and_packing_fields.php](file:///c:/xampp/htdocs/bewole-furniture/database/migrations/2026_08_27_000000_add_customization_and_packing_fields.php) berstatus `Pending` pada database. Migration ini juga memiliki timestamp prefix yang identik (`2026_08_27_000000_`) dengan migration Google ID. Akibatnya kolom `meubel_type`, `packing_type`, `customization_details`, `customization_fee`, `packing_fee` belum ada pada tabel `orders` MySQL, dan tabel `order_items` belum terbuat.

**Impact:**
Semua transaksi pemesanan melalui [CheckoutPage.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/CheckoutPage.php) gagal total.

**Related Files:**
* [OrderService.php](file:///c:/xampp/htdocs/bewole-furniture/app/Services/OrderService.php)
* [CheckoutPage.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/CheckoutPage.php)
* [Order.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/Order.php)
* [OrderItem.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/OrderItem.php)

**Recommended Solution:**
1. Berikan timestamp unik berurutan pada migration (misal: `2026_08_27_000001_add_customization_and_packing_fields.php`).
2. Jalankan `php artisan migrate` secara aman untuk mengeksekusi tabel `order_items` dan kolom pendukung tanpa mengganggu data yang sudah ada.

**Verification:**
Jalankan `php artisan migrate:status` untuk memastikan semua migration `Ran`, lalu lakukan tes pemesanan dari keranjang belanja.

**Dependencies:** None
**Confidence:** High

---

### ERR-002 — View Not Found (500 Error) pada Route Public & User

* **Category:** Route & View
* **Severity:** CRITICAL
* **Status:** OPEN
* **Feature:** Navigation & Frontend Pages

**Location:**
* [routes/web.php](file:///c:/xampp/htdocs/bewole-furniture/routes/web.php#L18-L22)
* [routes/web.php](file:///c:/xampp/htdocs/bewole-furniture/routes/web.php#L67)

**Symptom:**
Mengunjungi URL berikut menghasilkan `500 Server Error: View [frontend.xxx] not found`:
* `GET /portfolio` (`frontend.portfolio`)
* `GET /testimonials` (`frontend.testimonials`)
* `GET /faq` (`frontend.faq`)
* `GET /contact` (`frontend.contact`)
* `GET /profile` (`frontend.profile`)

**Root Cause:**
Pada [routes/web.php](file:///c:/xampp/htdocs/bewole-furniture/routes/web.php), terdapat deklarasi `Route::view()` yang mengarah ke file Blade yang tidak tersedia di dalam direktori [resources/views/frontend/](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/).
Khusus untuk:
* `/faq`: Konten FAQ sudah tersedia sebagai Blade component `<x-home.faq />` di homepage, namun route `/faq` belum memiliki view wrapper tersendiri.
* `/profile`: Route user mengarah ke `frontend.profile` yang belum ada, sementara Livewire profile settings ada di route `settings/profile` (`profile.edit`).

**Impact:**
User yang mengakses menu navigasi atau direct link ke halaman-halaman tersebut mengalami crash 500.

**Related Files:**
* [routes/web.php](file:///c:/xampp/htdocs/bewole-furniture/routes/web.php)
* [resources/views/frontend/](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/)

**Recommended Solution:**
1. Buat view Blade wrapper yang rapi dan konsisten dengan layout `frontend.layouts.app` untuk `portfolio`, `testimonials`, `faq`, dan `contact`.
2. Untuk `/profile`, arahkan route ke Livewire settings profile atau sediakan view dashboard user profile.

**Verification:**
Akses langsung seluruh endpoint public via browser dan verifikasi response HTTP 200.

**Dependencies:** None
**Confidence:** High

---

## 4. High Errors

### ERR-003 — Dataset Wilayah Indonesia Di-Ignore oleh Git (`indonesia_regions.json`)

* **Category:** Deployment / Core Function
* **Severity:** HIGH
* **Status:** OPEN
* **Feature:** Checkout & Shipping Selection

**Location:**
* [storage/app/.gitignore](file:///c:/xampp/htdocs/bewole-furniture/storage/app/.gitignore#L1)
* [storage/app/indonesia_regions.json](file:///c:/xampp/htdocs/bewole-furniture/storage/app/indonesia_regions.json)
* [CheckoutPage.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/CheckoutPage.php#L96)

**Symptom:**
Pada server production atau clone repositori baru, form checkout tidak menampilkan pilihan Provinsi dan Kota sama sekali (dropdown kosong), sehingga checkout tidak dapat diselesaikan.

**Root Cause:**
File [storage/app/.gitignore](file:///c:/xampp/htdocs/bewole-furniture/storage/app/.gitignore) memiliki aturan baris pertama `*` yang meng-ignore seluruh isi `storage/app/`, termasuk `indonesia_regions.json`. Karena file ini tidak masuk Git, file tidak akan ada di server production saat di-deploy via git pull.

**Impact:**
Checkout rusak total pada environment production / clean clone.

**Related Files:**
* [storage/app/.gitignore](file:///c:/xampp/htdocs/bewole-furniture/storage/app/.gitignore)
* [fetch_regions.php](file:///c:/xampp/htdocs/bewole-furniture/fetch_regions.php)
* [CheckoutPage.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/CheckoutPage.php)

**Recommended Solution:**
1. Tambahkan whitelist exception di [storage/app/.gitignore](file:///c:/xampp/htdocs/bewole-furniture/storage/app/.gitignore): `!indonesia_regions.json`.
2. Lakukan `git add -f storage/app/indonesia_regions.json` agar dataset wilayah terlacak di repositori Git.

**Verification:**
Jalankan `git status` dan pastikan file `storage/app/indonesia_regions.json` masuk ke dalam staging area git tracking.

**Dependencies:** None
**Confidence:** High

---

### ERR-004 — Fitur Review User Sisi Frontend Belum Terhubung (Integrasi Putus)

* **Category:** Incomplete Feature / Integration
* **Severity:** HIGH
* **Status:** OPEN
* **Feature:** Product Review & Rating

**Location:**
* [resources/views/livewire/frontend/product-detail.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/livewire/frontend/product-detail.blade.php)
* [ProductDetail.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/ProductDetail.php#L17)
* [routes/web.php](file:///c:/xampp/htdocs/bewole-furniture/routes/web.php)

**Symptom:**
1. User tidak memiliki antarmuka atau endpoint untuk memberikan ulasan/rating dan mengunggah foto review untuk produk yang telah dibeli.
2. Di halaman detail produk, ulasan yang berstatus aktif (`visibleReviews`) sudah di-load oleh controller tetapi tidak di-render pada template Blade.

**Root Cause:**
Hanya sisi Admin moderation ([ProductReviewController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/ProductReviewController.php)) dan Database model/migration yang telah dibuat. Komponen frontend untuk review belum diimplementasikan.

**Impact:**
Fitur ulasan produk (konsep utama USER) belum berfungsi untuk pembeli.

**Related Files:**
* [ProductDetail.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/ProductDetail.php)
* [resources/views/livewire/frontend/product-detail.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/livewire/frontend/product-detail.blade.php)
* [resources/views/frontend/orders/show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php)

**Recommended Solution:**
1. Buat komponen input ulasan produk (Rating bintang 1-5, komentar, upload foto review) pada detail pesanan yang berstatus `completed`.
2. Render daftar ulasan terverifikasi (`visibleReviews`) di bawah deskripsi pada [product-detail.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/livewire/frontend/product-detail.blade.php).

**Verification:**
Submit ulasan dari user yang telah menyelesaikan order, moderasi via admin, dan pastikan ulasan tampil di halaman produk.

**Dependencies:** None
**Confidence:** High

---

## 5. Medium Errors

### ERR-005 — Catatan Riwayat Status Pesanan Tidak Tampil pada Detail Pesanan User

* **Category:** Bug / Data Display
* **Severity:** MEDIUM
* **Status:** OPEN
* **Feature:** Order Tracking & User Orders

**Location:**
* [resources/views/frontend/orders/show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php#L122-L124)
* [OrderStatusHistory.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/OrderStatusHistory.php#L19)

**Symptom:**
Deskripsi perubahan status / catatan kurir / catatan produksi dari Admin tidak pernah muncul di linimasa riwayat status halaman detail pesanan user.

**Root Cause:**
Di [show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php#L122), kode memeriksa `@if ($history->notes)` dan menampilkan `{{ $history->notes }}`, padahal nama kolom database pada model [OrderStatusHistory.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/OrderStatusHistory.php) adalah `description`.

**Impact:**
User tidak dapat membaca detail catatan proses tracking pada halaman pesanan miliknya.

**Related Files:**
* [resources/views/frontend/orders/show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php)
* [OrderStatusHistory.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/OrderStatusHistory.php)

**Recommended Solution:**
Ganti pemanggilan `$history->notes` menjadi `$history->description` (atau `$history->description ?: $history->notes`).

**Verification:**
Buka halaman `/orders/{order_code}` setelah Admin mengubah status pesanan dengan catatan khusus, lalu pastikan catatan tersebut tampil di timeline.

**Dependencies:** None
**Confidence:** High

---

### ERR-006 — Mapping Status Badge Usang pada Halaman Detail Pesanan User

* **Category:** UI / Data Consistency
* **Severity:** MEDIUM
* **Status:** OPEN
* **Feature:** Order Status Display

**Location:**
* [resources/views/frontend/orders/show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php#L140-L148)

**Symptom:**
Status pesanan seperti `in_production`, `quality_control`, `ready_to_ship`, `awaiting_payment`, `payment_received`, dan `shipped` selalu tampil menggunakan warna badge abu-abu fallback `@else bg-gray-50 text-gray-600`.

**Root Cause:**
Kondisi `@if` pada Blade masih menggunakan nilai status lama (`processing`, `shipping`) yang tidak sesuai dengan nilai enum aktual pada [OrderStatus.php](file:///c:/xampp/htdocs/bewole-furniture/app/Enums/OrderStatus.php).

**Impact:**
Tampilan visual status pesanan tidak mencerminkan tahapan pengerjaan meubel dengan tepat.

**Related Files:**
* [resources/views/frontend/orders/show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php)
* [OrderStatus.php](file:///c:/xampp/htdocs/bewole-furniture/app/Enums/OrderStatus.php)

**Recommended Solution:**
Gunakan accessor model `$order->status_color` atau perbarui `@if` blade agar mencakup seluruh case enum `OrderStatus`.

**Verification:**
Uji coba pesanan dengan berbagai status enum dan pastikan warna badge sesuai (misal: `in_production` cokelat kayu, `completed` hijau emerald).

**Dependencies:** None
**Confidence:** High

---

### ERR-007 — Method Resource Tidak Lengkap pada Controller Admin (Stub Controllers)

* **Category:** Route & Controller
* **Severity:** MEDIUM
* **Status:** OPEN
* **Feature:** Admin Resource Endpoints

**Location:**
* [routes/admin.php](file:///c:/xampp/htdocs/bewole-furniture/routes/admin.php#L13-L38)
* [PortfolioController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/PortfolioController.php)
* [TestimonialController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/TestimonialController.php)
* [ProductImageController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/ProductImageController.php)
* [SettingController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/SettingController.php)

**Symptom:**
Jika ada request HTTP selain `GET /` yang memanggil `create`, `store`, `show`, `edit`, `update`, atau `destroy` pada route `admin.portfolios`, `admin.testimonials`, `admin.product-images`, atau `admin.settings`, Laravel akan melempar `BadMethodCallException: Method ... does not exist`.

**Root Cause:**
Route didaftarkan menggunakan `Route::resource(...)` penuh, padahal controllernya hanya menyediakan method `index()`.

**Impact:**
Route list tercemar endpoint mati (*dead endpoints*) dan memicu 500 error bila diakses melalui form request atau HTTP request standar.

**Related Files:**
* [routes/admin.php](file:///c:/xampp/htdocs/bewole-furniture/routes/admin.php)

**Recommended Solution:**
Gunakan pembatasan route explicit seperti `Route::resource(...)->only(['index'])` atau `Route::get(...)` spesifik sesuai implementasi aktual.

**Verification:**
Jalankan `php artisan route:list --path=admin` dan pastikan tidak ada endpoint phantom tanpa method controller.

**Dependencies:** None
**Confidence:** High

---

## 6. Low Issues

### ERR-008 — Key Background Login Tidak Sinkron dengan Accessor Model

* **Category:** UI / Admin Branding
* **Severity:** LOW
* **Status:** OPEN
* **Feature:** Website Settings & Auth Layout

**Location:**
* [resources/views/layouts/auth/login.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/layouts/auth/login.blade.php#L4)
* [WebsiteSettingService.php](file:///c:/xampp/htdocs/bewole-furniture/app/Services/WebsiteSettingService.php#L107)
* [WebsiteSetting.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/WebsiteSetting.php)

**Symptom:**
Gambar background custom untuk halaman login yang diunggah Admin melalui Website Settings tidak pernah tampil di halaman login auth.

**Root Cause:**
[login.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/layouts/auth/login.blade.php#L4) mencari key `login_background_url`, namun [WebsiteSettingService.php](file:///c:/xampp/htdocs/bewole-furniture/app/Services/WebsiteSettingService.php) hanya menyediakan key `login_background` mentah tanpa URL storage builder.

**Impact:**
Pengaturan custom background login dari Admin tidak berdampak visual.

**Related Files:**
* [resources/views/layouts/auth/login.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/layouts/auth/login.blade.php)
* [WebsiteSettingService.php](file:///c:/xampp/htdocs/bewole-furniture/app/Services/WebsiteSettingService.php)
* [WebsiteSetting.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/WebsiteSetting.php)

**Recommended Solution:**
Tambahkan accessor `getLoginBackgroundUrlAttribute()` pada model [WebsiteSetting.php](file:///c:/xampp/htdocs/bewole-furniture/app/Models/WebsiteSetting.php) dan sertakan di `WebsiteSettingService::getForFrontend()`.

**Verification:**
Upload background di Admin Settings -> Buka `/login` -> Pastikan background termuat.

**Dependencies:** None
**Confidence:** High

---

### ERR-009 — Middleware Maintenance Mode Memblokir Admin dari Preview Frontend

* **Category:** Admin / UX
* **Severity:** LOW
* **Status:** OPEN
* **Feature:** Maintenance Mode

**Location:**
* [CheckMaintenanceMode.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Middleware/CheckMaintenanceMode.php#L20-L28)

**Symptom:**
Ketika Maintenance Mode diaktifkan via Admin Website Settings, Admin yang sedang login sekalipun tidak dapat melihat preview halaman depan website karena langsung dialihkan ke halaman 503 Maintenance.

**Root Cause:**
Middleware [CheckMaintenanceMode.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Middleware/CheckMaintenanceMode.php) hanya mengecualikan path `admin/*`, `login`, `register`, `dashboard` berdasarkan URL pattern, tanpa mengecek apakah user yang sedang terautentikasi memiliki role `admin`.

**Impact:**
Admin harus mematikan maintenance mode secara publik terlebih dahulu jika ingin memeriksa tampilan frontend.

**Related Files:**
* [CheckMaintenanceMode.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Middleware/CheckMaintenanceMode.php)

**Recommended Solution:**
Tambahkan pengecekan role pada middleware:
```php
if (auth()->check() && auth()->user()->hasRole('admin')) {
    return $next($request);
}
```

**Verification:**
Aktifkan maintenance mode -> Login sebagai Admin -> Buka `/` (Homepage) -> Halaman harus dapat terbuka untuk Admin.

**Dependencies:** None
**Confidence:** High

---

## 7. Security Findings

| Aspek Keamanan | Status | Analisis & Bukti |
| :--- | :---: | :--- |
| **CSRF Protection** | ✅ SECURE | Web middleware menyertakan `VerifyCsrfToken`, form memiliki `@csrf`, dan Livewire komponen menggunakan signed payload. |
| **Price Tampering Prevention** | ✅ SECURE | Di [CheckoutPage.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/CheckoutPage.php#L158-L197), harga barang dan subtotal selalu dihitung ulang dari database produk aktif di sisi server, bukan dari client request. |
| **IDOR (Insecure Direct Object Reference)** | ✅ SECURE | [OrderController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Frontend/OrderController.php#L30) memvalidasi `if ($order->user_id !== auth()->id()) abort(403)`. Admin controller dilindungi [OrderPolicy.php](file:///c:/xampp/htdocs/bewole-furniture/app/Policies/OrderPolicy.php). |
| **Role-Based Access Control (RBAC)** | ✅ SECURE | Endpoint admin dilindungi middleware `role:admin` dari Spatie Permission di [routes/admin.php](file:///c:/xampp/htdocs/bewole-furniture/routes/admin.php#L6). |
| **File Upload Validation** | ✅ SECURE | Request class memvalidasi `image`, `mimes:jpg,jpeg,png,webp`, dan batas ukuran `max:5120` (5MB). |
| **Mass Assignment** | ✅ SECURE | Semua model menggunakan whitelist `$fillable` eksplisit. |
| **SQL Injection** | ✅ SECURE | Penggunaan Eloquent ORM dan parameter binding query builder di seluruh controller dan service. |

---

## 8. Database Findings

1. **Status Migration Pending:**
   `2026_08_27_000000_add_customization_and_packing_fields.php` berstatus `Pending`.
2. **Duplikasi Timestamp Prefix Migration:**
   * `2026_08_27_000000_add_customization_and_packing_fields.php`
   * `2026_08_27_000000_add_google_id_to_users_table.php`
   Kedua file memiliki prefix timestamp yang sama persis sehingga dapat membingungkan urutan eksekusi migration generator.
3. **Foreign Key Constraints:**
   Semua relasi `orders.user_id`, `orders.product_id`, `order_status_histories.order_id`, `product_images.product_id` memiliki foreign key constraint dengan action cascade/null-on-delete yang tepat.

---

## 9. Authentication Findings

1. **Email & Password Registration:** Berfungsi normal dengan hashing Bcrypt, default role `user` otomatis diberikan via [CreateNewUser.php](file:///c:/xampp/htdocs/bewole-furniture/app/Actions/Fortify/CreateNewUser.php).
2. **Standard Login & Fortify:** Responses diarahkan dengan tepat via [LoginResponse.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Responses/LoginResponse.php) (Admin -> `/admin/dashboard`, User -> `/`).
3. **Google OAuth (Socialite):**
   * Controller [GoogleController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Auth/GoogleController.php) memiliki arsitektur lengkap: pengecekan `google_id`, linking akun email yang sudah ada, pembuatan user baru dengan role `user`, auto-verification email, dan session regeneration.
   * `NEEDS CONFIGURATION`: Kredensial `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` masih kosong di `.env`.
4. **Forgot & Reset Password:** Menggunakan Fortify action [ResetUserPassword.php](file:///c:/xampp/htdocs/bewole-furniture/app/Actions/Fortify/ResetUserPassword.php) dengan template Blade premium bernuansa Bewole.

---

## 10. Order Findings

1. **Alur Checkout Server-Side:**
   Cart -> [CheckoutPage.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/CheckoutPage.php) -> [OrderService.php](file:///c:/xampp/htdocs/bewole-furniture/app/Services/OrderService.php) -> Database -> WhatsApp Redirect.
2. **Validasi Wajib Login:**
   Checkout dilindungi middleware `['auth', 'verified', 'maintenance']` di [routes/web.php](file:///c:/xampp/htdocs/bewole-furniture/routes/web.php#L50).
3. **Order Code Generator:**
   Kode unik di-generate secara otomatis berformat standar (misal: `BWL-YYYYMMDD-XXXX`).
4. **Bloker Utama:**
   Penyimpanan order saat ini crash karena migration pending (`ERR-001`).

---

## 11. Tracking Findings

1. **Order Tracking Page (`/tracking`):**
   * Menggunakan Livewire component [OrderTracking.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Frontend/OrderTracking.php).
   * **Tidak Hardcoded:** Data diambil dinamis langsung dari tabel `orders` dan `order_status_histories`.
   * **Stepper Progress Bar:** Memiliki 5 tahapan dinamis yang sinkron dengan status database:
     * Step 1: Pesanan Dikonfirmasi (`pending`, `confirmed`)
     * Step 2: Pembayaran (`awaiting_payment`, `payment_received`)
     * Step 3: Produksi & QC (`in_production`, `quality_control`)
     * Step 4: Pengiriman (`ready_to_ship`, `shipped`)
     * Step 5: Selesai (`completed`)
     * Alert Khusus: Dibatalkan (`cancelled`)
2. **Pembaruan Status oleh Admin:**
   Admin memperbarui status via [OrderStatusManager.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Admin/Order/OrderStatusManager.php) yang memicu event `OrderService::updateStatus` dan otomatis mencatat riwayat ke `order_status_histories`.

---

## 12. Admin Findings

1. **Dashboard:** [Admin Dashboard](file:///c:/xampp/htdocs/bewole-furniture/resources/views/admin/dashboard/index.blade.php) memuat statistik ringkasan order, omzet, dan chart bulanan.
2. **Kategori:** Dikelola penuh melalui Livewire [CategoryManager.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Admin/Category/CategoryManager.php) (CRUD + Upload Cover).
3. **Produk:** Dikelola via [ProductController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/ProductController.php) (CRUD + Thumbnail + Multi-image Gallery + Auto-Slug + Auto-Discount Calculation).
4. **Pesanan:** Dikelola via [OrderTable.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Admin/Order/OrderTable.php) + Invoice PDF generator [OrderController::invoice](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/OrderController.php#L171).
5. **Hero Banner:** CRUD lengkap di [HeroBannerController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/HeroBannerController.php).
6. **FAQ:** CRUD lengkap di [FaqController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Admin/FaqController.php).
7. **Laporan & Export:** Livewire [OrderReport.php](file:///c:/xampp/htdocs/bewole-furniture/app/Livewire/Admin/Report/OrderReport.php) dengan fitur export Excel via Maatwebsite Excel ([OrderReportExport.php](file:///c:/xampp/htdocs/bewole-furniture/app/Exports/OrderReportExport.php)) dan PDF.

---

## 13. Audit Khusus Hero / Banner (Posisi Teks & Tampilan)

Audit mendalam terhadap alur:
$$\text{Admin Input} \to \text{Controller} \to \text{Database} \to \text{Model} \to \text{HomeController} \to \text{Blade} \to \text{Tailwind}$$

**Hasil Penelusuran:**
1. **Admin Input:** Dropdown `text_position` (`left`, `center`, `right`) tersimpan dengan benar ke kolom enum `hero_banners.text_position`.
2. **Frontend Query:** [HomeController.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Controllers/Frontend/HomeController.php#L15) mengambil hero aktif via `HeroBanner::active()->sorted()->first()`.
3. **Blade Template:** Di [resources/views/frontend/partials/hero.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/partials/hero.blade.php#L22-L58), pemetaan posisi dilakukan melalui `$positionMap[$position]` yang mendefinisikan class Tailwind:
   * `left`: `wrapper => justify-start`, `box => mr-auto items-start text-left`, `title => text-left`
   * `center`: `wrapper => justify-center`, `box => mx-auto items-center text-center`, `title => text-center mx-auto`
   * `right`: `wrapper => justify-end`, `box => ml-auto items-end text-right`, `title => text-right ml-auto`
4. **Vite & Tailwind Build:** Seluruh class tersebut berada langsung di dalam file Blade sehingga terdeteksi secara statis oleh parser `@tailwindcss/vite` tanpa terpotong (purged).
5. **Potensi Masalah Sebelumnya:** Kompilasi view Blade pernah ter-cache (`Views .. CACHED` di `artisan about`). Jika admin mengubah data tetapi view cache tidak dibersihkan atau browser menyimpan cache HTML, perubahan dapat terlihat tidak langsung berubah.

---

## 14. UI/UX & Design System Findings

* **Kesesuaian Tema:** Palet warna utama menggunakan kombinasi elegan *Dark Wood & Gold/Beige* (`#5B3A29`, `#422818`, `#A67C52`, `#F7F4EF`) yang sangat cocok untuk brand furniture luxury/custom Jepara.
* **Glassmorphism & Navbar:** [navbar.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/partials/navbar.blade.php) menggunakan styling *liquid-glass* dengan transisi scroll state yang mulus.
* **Iconography:** Menggunakan SVG vector murni dan Font Awesome yang seragam tanpa emoji sebagai icon UI.
* **Responsiveness:** Desktop navbar tampil bersih tanpa hamburger, sedangkan mobile otomatis menampilkan hamburger drawer.

---

## 15. Vite & Tailwind Findings

* **Tailwind Version:** Tailwind CSS v4.3.3 dengan `@tailwindcss/vite`.
* **Build Status:** Perintah `npm run build` berhasil dijalankan (**0 error**) dalam 3.32 detik dengan asset output:
  * `public/build/assets/app-COesTdqS.css` (218.48 kB)
  * `public/build/assets/frontend-BJGYmt1T.js` (2.81 kB)
  * `public/build/manifest.json` (0.65 kB)
* **CSS Directives:** [app.css](file:///c:/xampp/htdocs/bewole-furniture/resources/css/app.css) mengonfigurasi `@source "../**/*.blade.php"` dan `@theme` token secara terstruktur.

---

## 16. Deployment Findings

| Komponen | Status | Catatan |
| :--- | :---: | :--- |
| **APP_KEY** | ✅ READY | `APP_KEY` terkonfigurasi. |
| **Storage Link** | ✅ READY | `public/storage` telah terhubung ke `storage/app/public`. |
| **Vite Production Assets** | ✅ READY | Bundle `public/build` sudah ter-generate. |
| **Database Connection** | ✅ LOCAL (MySQL) | Database `furniture` aktif di MySQL lokal. |
| **Google OAuth** | ⚠️ NEEDS CONFIGURATION | Memerlukan Client ID & Secret Google Console di `.env`. |
| **Mail Server** | ⚠️ NEEDS CONFIGURATION | `MAIL_MAILER=log` untuk lokal; perlu SMTP/SES untuk produksi. |
| **Wilayah Dataset** | ❌ BROKEN | `storage/app/indonesia_regions.json` belum di-track oleh Git (`ERR-003`). |

---

## 17. Feature Completion Matrix

| Feature | Frontend | Backend | Database | Integration | Tested | Status |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **Authentication (Login/Register)** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Google Login** | ✅ | ✅ | ✅ | ⚠️ | ⚠️ | **NEEDS CONFIGURATION** |
| **Forgot & Reset Password** | ✅ | ✅ | ✅ | ✅ | ⚠️ | **COMPLETE** |
| **Product Catalog & Detail** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Category Management** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Cart System** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Checkout & Order Creation** | ✅ | ✅ | ❌ | ❌ | ❌ | **BROKEN (ERR-001)** |
| **WhatsApp Order Redirect** | ✅ | ✅ | ❌ | ❌ | ❌ | **BLOCKED (ERR-001)** |
| **Order Tracking & Stepper** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **User Product Reviews** | ❌ | ⚠️ | ✅ | ❌ | ❌ | **NOT STARTED (ERR-004)** |
| **Admin Review Moderation** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Admin Orders & Status Manager** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Admin Hero / Banner** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Admin Website Settings** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **Company Profile Management** | ✅ | ✅ | ✅ | ✅ | ✅ | **COMPLETE** |
| **FAQ System** | ⚠️ | ✅ | ✅ | ⚠️ | ⚠️ | **PARTIAL (ERR-002)** |
| **Portfolio & Testimonials** | ❌ | ⚠️ | ✅ | ❌ | ❌ | **PARTIAL (ERR-002)** |
| **Vite & Tailwind Build** | ✅ | ✅ | N/A | ✅ | ✅ | **COMPLETE** |

---

## 18. Error Matrix

| ID | Category | Severity | Status | Feature | File | Problem | Root Cause | Solution | Confidence |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **ERR-001** | Database | **CRITICAL** | VERIFIED | Checkout / Order | `OrderService.php` | Order crash saat checkout | Migration pending & tabel `order_items` belum ada di MySQL | Fix timestamp migration & jalankan migrate | High |
| **ERR-002** | Route/View | **CRITICAL** | OPEN | Navigation | `routes/web.php` | 500 View Not Found di 5 route | View Blade `portfolio`, `testimonials`, `faq`, `contact`, `profile` belum dibuat | Buat view Blade wrapper yang sesuai | High |
| **ERR-003** | Deployment | **HIGH** | OPEN | Checkout Shipping | `storage/app/.gitignore` | Dropdown provinsi kosong di clean deploy | `indonesia_regions.json` ter-ignore oleh Git | Tambahkan whitelist exception di `.gitignore` & commit file | High |
| **ERR-004** | Incomplete | **HIGH** | OPEN | Review Produk | `product-detail.blade.php` | User tidak bisa submit review & review tidak tampil | Komponen input & list ulasan frontend belum dibuat | Buat form review user & render review list | High |
| **ERR-005** | Bug | **MEDIUM** | OPEN | Order History | `orders/show.blade.php` | Catatan riwayat status tidak muncul | Pengecekan property salah (`notes` vs `description`) | Ganti `$history->notes` ke `$history->description` | High |
| **ERR-006** | UI | **MEDIUM** | OPEN | Order Badge | `orders/show.blade.php` | Badge status order selalu abu-abu | Kondisi `@if` menggunakan status usang (`processing`/`shipping`) | Sinkronkan kondisi status dengan enum `OrderStatus` | High |
| **ERR-007** | Route | **MEDIUM** | OPEN | Admin Resource | `routes/admin.php` | `BadMethodCallException` di route resource admin | Route resource penuh didaftarkan untuk controller stub | Batasi route dengan `->only(['index'])` | High |
| **ERR-008** | UI | **LOW** | OPEN | Branding | `auth/login.blade.php` | Custom login background tidak muncul | Key setting tidak sinkron (`login_background_url`) | Tambahkan accessor URL pada model & service | High |
| **ERR-009** | UX | **LOW** | OPEN | Maintenance | `CheckMaintenanceMode.php` | Admin terblokir halaman 503 saat preview | Middleware tidak mengecualikan user dengan role `admin` | Bypass maintenance mode jika user ber-role admin | High |
| **ERR-010** | Config | **INFO** | OPEN | Dependencies | `composer.json` | Peringatan unbound version wildcard `*` | Constraint `*` pada socialite dan excel | Ganti dengan version constraint semantik (misal: `^5.0`) | High |
| **ERR-011** | Cleanup | **INFO** | OPEN | Dead Code | `resources/views/welcome.blade.php` | File starter template tidak terpakai (64KB) | File default Laravel welcome belum dibersihkan | Hapus file dead code yang tidak digunakan | High |

---

## 19. Dependency Map

```text
┌────────────────────────────────────────────────────────┐
│                        ERR-001                         │
│ Migration Pending & Missing Database Columns/Tables    │
└──────────────────────────┬─────────────────────────────┘
                           │
                           ▼
┌────────────────────────────────────────────────────────┐
│                        ERR-003                         │
│ Missing Regions Dataset (Checkout Fails Form Validation)│
└──────────────────────────┬─────────────────────────────┘
                           │
                           ▼
┌────────────────────────────────────────────────────────┐
│                 Checkout Flow Failure                  │
│ Order Cannot Be Saved -> WhatsApp Redirect Never Fires │
└────────────────────────────────────────────────────────┘
```

```text
┌────────────────────────────────────────────────────────┐
│                        ERR-002                         │
│ Missing Frontend Blade View Files in resources/views/  │
└──────────────────────────┬─────────────────────────────┘
                           │
                           ▼
┌────────────────────────────────────────────────────────┐
│           HTTP 500 Server Crash On Direct URL          │
│ (/portfolio, /testimonials, /faq, /contact, /profile)  │
└──────────────────────────┘
```

---

## 20. Recommended Fix Order

### PRIORITY 1 — BLOCKERS (Wajib Pertama)
1. **Fix ERR-001:** Rapikan timestamp migration `2026_08_27_...` dan jalankan `php artisan migrate` agar tabel `order_items` dan kolom meubel/packing terbentuk di MySQL.
2. **Fix ERR-003:** Buka ignore `storage/app/indonesia_regions.json` pada `.gitignore` dan lakukan tracking git agar dataset provinsi/kota selalu tersedia.
3. **Fix ERR-002:** Buat file view Blade yang hilang untuk `/portfolio`, `/testimonials`, `/faq`, `/contact`, dan `/profile` agar tidak ada route 500.

### PRIORITY 2 — CORE FUNCTION & INTEGRATION
4. **Fix ERR-004:** Hubungkan fitur review produk sisi user (form review pada order selesai + list review di product detail).
5. **Fix ERR-005:** Perbaiki property `$history->description` pada [resources/views/frontend/orders/show.blade.php](file:///c:/xampp/htdocs/bewole-furniture/resources/views/frontend/orders/show.blade.php).

### PRIORITY 3 — UI & CONSISTENCY
6. **Fix ERR-006:** Sinkronkan pemetaan status badge order dengan enum `OrderStatus`.
7. **Fix ERR-008:** Perbaiki accessor `login_background_url` untuk kustomisasi branding login.
8. **Fix ERR-009:** Perbarui [CheckMaintenanceMode.php](file:///c:/xampp/htdocs/bewole-furniture/app/Http/Middleware/CheckMaintenanceMode.php) agar Admin login dapat mengakses frontend preview saat maintenance aktif.

### PRIORITY 4 — CLEANUP & DEPLOYMENT
9. **Fix ERR-007:** Batasi route resource admin stub di [routes/admin.php](file:///c:/xampp/htdocs/bewole-furniture/routes/admin.php).
10. **Fix ERR-010 & ERR-011:** Bersihkan version constraint `composer.json` dan hapus file dead code.

---

## 21. Verification Checklist

Setelah instruksi perbaikan diberikan, checklist ini akan digunakan untuk memvalidasi setiap perbaikan:

```text
[ ] Database migration status verified (All migrations "Ran", no duplicate timestamps)
[ ] Order checkout tested from cart with meubel type & packing options
[ ] Order items saved in database and WhatsApp message generated correctly
[ ] All public routes (about, portfolio, testimonials, faq, contact, tracking, products) return HTTP 200
[ ] Profile route accessible by authenticated users
[ ] Indonesia regions JSON tracked and loaded in checkout dropdown
[ ] User product review submission tested on completed order
[ ] Product detail displays approved/visible reviews
[ ] Order tracking step bar & history timeline verified with dynamic database status
[ ] Order status change notes by Admin visible to user
[ ] Admin resource routes verified without BadMethodCallException
[ ] Admin preview during maintenance mode tested
[ ] npm run build passes without warnings or missing assets
[ ] Production environment configuration checklist verified
```

---

## 22. Status: NEEDS VERIFICATION

* `NEEDS VERIFICATION — Google OAuth Production Credentials`: Konfigurasi `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` belum diisi pada `.env` (wajar untuk environment lokal, namun wajib disiapkan sebelum peluncuran produksi).
* `NEEDS VERIFICATION — Mail Driver Production`: Driver email saat ini adalah `MAIL_MAILER=log`, reset password link dicatat ke log file bukan dikirim ke inbox user aktual.
