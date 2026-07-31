<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Faq::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questions = [
            'Apakah produk furnitur Bewole tersedia di seluruh Indonesia?',
            'Berapa lama waktu pengiriman produk furnitur?',
            'Apakah ada garansi untuk produk yang dibeli?',
            'Bagaimana cara merawat furnitur kayu?',
            'Apakah tersedia jasa perakitan furnitur?',
            'Bahan apa saja yang digunakan untuk membuat furnitur?',
            'Apakah bisa memesan furnitur dengan ukuran custom?',
            'Bagaimana jika produk yang diterima rusak?',
            'Apakah tersedia layanan konsultasi desain interior?',
            'Metode pembayaran apa saja yang tersedia?',
        ];

        $answers = [
            'Ya, kami melayani pengiriman ke seluruh Indonesia. Biaya pengiriman akan disesuaikan dengan lokasi tujuan dan volume barang. Untuk wilayah Jawa, estimasi pengiriman 2-5 hari kerja, sedangkan luar Jawa 5-14 hari kerja.',
            'Waktu pengiriman bervariasi tergantung lokasi. Untuk wilayah Jawa, estimasi 2-5 hari kerja. Luar Jawa 5-14 hari kerja. Untuk produk pre-order, waktu pengiriman akan diinformasikan oleh tim kami setelah pemesanan.',
            'Setiap produk furnitur Bewole dilengkapi garansi 1 tahun untuk kerusakan material dan pengerjaan. Garansi tidak berlaku untuk kerusakan akibat penggunaan yang tidak sesuai atau force majeure.',
            'Untuk merawat furnitur kayu, hindari paparan sinar matahari langsung dan kelembaban berlebih. Bersihkan secara rutin menggunakan kain lembut yang sedikit dibasahi. Gunakan furniture polish khusus kayu setiap 3 bulan sekali.',
            'Ya, kami menyediakan jasa perakitan furnitur dengan biaya tambahan. Tim kami akan datang ke lokasi dan merakit furnitur yang Anda beli. Silakan hubungi customer service untuk informasi lebih lanjut.',
            'Kami menggunakan berbagai bahan berkualitas tinggi seperti kayu jati, mahoni, rotan, dan material premium lainnya. Setiap bahan dipilih secara selektif untuk memastikan kualitas dan ketahanan produk.',
            'Tentu, kami melayani pemesanan furnitur dengan ukuran custom. Silakan konsultasikan kebutuhan Anda dengan tim kami, termasuk desain, ukuran, dan bahan yang diinginkan. Proses custom biasanya memakan waktu 2-4 minggu.',
            'Jika produk yang diterima dalam kondisi rusak, segera hubungi customer service kami dalam waktu maksimal 2x24 jam setelah barang diterima. Sertakan foto atau video sebagai bukti. Kami akan mengganti produk yang rusak tanpa biaya tambahan.',
            'Ya, kami memiliki layanan konsultasi desain interior yang dapat membantu Anda memilih dan menata furnitur sesuai dengan konsep ruangan yang diinginkan. Konsultasi dapat dilakukan secara online ataupun offline.',
            'Kami menerima berbagai metode pembayaran termasuk transfer bank (BCA, Mandiri, BRI, BNI), kartu kredit, e-wallet (GoPay, OVO, Dana), dan pembayaran melalui marketplace. Tersedia juga cicilan 0% untuk kartu kredit tertentu.',
        ];

        $index = $this->faker->unique()->numberBetween(0, count($questions) - 1);

        return [
            'question' => $questions[$index] ?? $this->faker->sentence(),
            'answer' => $answers[$index] ?? $this->faker->paragraphs(3, true),
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}

