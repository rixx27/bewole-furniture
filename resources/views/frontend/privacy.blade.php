@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
@endphp

<x-frontend.layout :title="'Kebijakan Privasi - ' . $siteName">
    <div class="bg-wood-50/50 py-12 md:py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-wood-200/70 bg-white p-6 md:p-12 shadow-xs">
                <h1 class="text-3xl font-serif font-bold text-wood-900 mb-6">Kebijakan Privasi</h1>
                <p class="text-sm text-wood-500 mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>

                <div class="prose prose-wood max-w-none text-wood-700 space-y-6 text-sm leading-relaxed">
                    <p>
                        Selamat datang di <strong>{{ $siteName }}</strong>. Kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi informasi pribadi yang Anda bagikan kepada kami saat menggunakan layanan dan situs web kami di <strong>https://bewolejepara.com</strong>.
                    </p>

                    <h2 class="text-lg font-semibold text-wood-900 pt-2">1. Informasi yang Kami Kumpulkan</h2>
                    <p>
                        Kami dapat mengumpulkan informasi saat Anda mendaftar akun, melakukan pemesanan, atau menggunakan layanan masuk dengan pihak ketiga seperti Google OAuth:
                    </p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Informasi Akun:</strong> Nama lengkap, alamat email, nomor telepon/WhatsApp, dan alamat pengiriman.</li>
                        <li><strong>Informasi Masuk Google:</strong> Jika Anda memilih masuk menggunakan Google, kami hanya menerima nama, alamat email, dan foto profil publik Anda sesuai izin yang Anda berikan.</li>
                        <li><strong>Data Transaksi:</strong> Rincian pesanan furniture, bukti transfer pembayaran, dan catatan komunikasi pemesanan.</li>
                    </ul>

                    <h2 class="text-lg font-semibold text-wood-900 pt-2">2. Penggunaan Informasi</h2>
                    <p>Informasi yang kami kumpulkan digunakan untuk:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Memproses, mengonfirmasi, dan mengirimkan pesanan furniture Anda.</li>
                        <li>Memudahkan autentikasi dan pengelolaan akun pengguna Anda.</li>
                        <li>Memberikan layanan bantuan pelanggan serta pembaruan status pesanan.</li>
                        <li>Meningkatkan kualitas produk, layanan, dan pengalaman belanja di situs web kami.</li>
                    </ul>

                    <h2 class="text-lg font-semibold text-wood-900 pt-2">3. Perlindungan & Keamanan Data</h2>
                    <p>
                        Kami menerapkan langkah-langkah keamanan teknis untuk menjaga keamanan informasi pribadi Anda. Kami tidak akan menjual, menyewakan, atau memberikan informasi pribadi Anda kepada pihak ketiga mana pun tanpa persetujuan Anda, kecuali diwajibkan oleh hukum atau diperlukan untuk pengiriman pesanan (misalnya kurir kargo pengiriman furniture).
                    </p>

                    <h2 class="text-lg font-semibold text-wood-900 pt-2">4. Hak Pengguna</h2>
                    <p>
                        Anda berhak memperbarui, mengoreksi, atau meminta penghapusan data akun Anda kapan saja dengan menghubungi kami melalui halaman Kontak atau layanan WhatsApp resmi kami.
                    </p>

                    <h2 class="text-lg font-semibold text-wood-900 pt-2">5. Kontak Kami</h2>
                    <p>
                        Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami melalui:
                    </p>
                    <p class="font-medium">
                        Email: info@bewolejepara.com / krisnafrd27@gmail.com<br>
                        Situs Web: <a href="https://bewolejepara.com" class="text-wood-700 underline">https://bewolejepara.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-frontend.layout>
