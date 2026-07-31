<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Apakah produk furnitur Bewole tersedia di seluruh Indonesia?',
                'answer' => 'Ya, kami melayani pengiriman ke seluruh Indonesia. Biaya pengiriman akan disesuaikan dengan lokasi tujuan dan volume barang. Untuk wilayah Jawa, estimasi pengiriman 2-5 hari kerja, sedangkan luar Jawa 5-14 hari kerja.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa lama waktu pengiriman produk furnitur?',
                'answer' => 'Waktu pengiriman bervariasi tergantung lokasi. Untuk wilayah Jawa, estimasi 2-5 hari kerja. Luar Jawa 5-14 hari kerja. Untuk produk pre-order, waktu pengiriman akan diinformasikan oleh tim kami setelah pemesanan.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah ada garansi untuk produk yang dibeli?',
                'answer' => 'Setiap produk furnitur Bewole dilengkapi garansi 1 tahun untuk kerusakan material dan pengerjaan. Garansi tidak berlaku untuk kerusakan akibat penggunaan yang tidak sesuai atau force majeure.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara merawat furnitur kayu?',
                'answer' => 'Untuk merawat furnitur kayu, hindari paparan sinar matahari langsung dan kelembaban berlebih. Bersihkan secara rutin menggunakan kain lembut yang sedikit dibasahi. Gunakan furniture polish khusus kayu setiap 3 bulan sekali.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah tersedia jasa perakitan furnitur?',
                'answer' => 'Ya, kami menyediakan jasa perakitan furnitur dengan biaya tambahan. Tim kami akan datang ke lokasi dan merakit furnitur yang Anda beli. Silakan hubungi customer service untuk informasi lebih lanjut.',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Bahan apa saja yang digunakan untuk membuat furnitur?',
                'answer' => 'Kami menggunakan berbagai bahan berkualitas tinggi seperti kayu jati, mahoni, rotan, dan material premium lainnya. Setiap bahan dipilih secara selektif untuk memastikan kualitas dan ketahanan produk.',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah bisa memesan furnitur dengan ukuran custom?',
                'answer' => 'Tentu, kami melayani pemesanan furnitur dengan ukuran custom. Silakan konsultasikan kebutuhan Anda dengan tim kami, termasuk desain, ukuran, dan bahan yang diinginkan. Proses custom biasanya memakan waktu 2-4 minggu.',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana jika produk yang diterima rusak?',
                'answer' => 'Jika produk yang diterima dalam kondisi rusak, segera hubungi customer service kami dalam waktu maksimal 2x24 jam setelah barang diterima. Sertakan foto atau video sebagai bukti. Kami akan mengganti produk yang rusak tanpa biaya tambahan.',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah tersedia layanan konsultasi desain interior?',
                'answer' => 'Ya, kami memiliki layanan konsultasi desain interior yang dapat membantu Anda memilih dan menata furnitur sesuai dengan konsep ruangan yang diinginkan. Konsultasi dapat dilakukan secara online ataupun offline.',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'question' => 'Metode pembayaran apa saja yang tersedia?',
                'answer' => 'Kami menerima berbagai metode pembayaran termasuk transfer bank (BCA, Mandiri, BRI, BNI), kartu kredit, e-wallet (GoPay, OVO, Dana), dan pembayaran melalui marketplace. Tersedia juga cicilan 0% untuk kartu kredit tertentu.',
                'sort_order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}

