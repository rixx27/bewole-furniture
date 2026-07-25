<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userCount = User::count();
        $productCount = Product::count();
        $orderCount = Order::count();

        if ($userCount === 0 || $productCount === 0 || $orderCount === 0) {
            $this->command->warn('Users, Products, or Orders table is empty. Skipping ProductReviewSeeder.');
            $this->command->warn('Please seed Users, Products, and Orders first.');

            return;
        }

        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();
        $orderIds = Order::pluck('id')->toArray();

        $usedCombinations = [];
        $createdCount = 0;

        $indonesianNames = [
            'Adi Pratama', 'Budi Santoso', 'Citra Dewi', 'Dian Permata', 'Eko Saputra',
            'Fitri Handayani', 'Gilang Ramadhan', 'Hesti Purwanti', 'Indra Wijaya', 'Joko Susilo',
            'Kartika Sari', 'Lukman Hakim', 'Maya Anggraini', 'Nugroho Setiawan', 'Olivia Tan',
            'Putra Nugraha', 'Ratna Kusuma', 'Sigit Pramono', 'Tari Rahmawati', 'Umar Fauzi',
            'Vina Lestari', 'Wahyu Hidayat', 'Yanti Puspita', 'Zainal Arifin', 'Agung Prasetyo',
            'Rina Marlina', 'Doni Kurniawan', 'Sari Febrianti', 'Rudi Hartono', 'Nina Wulandari',
            'Bambang Suprapto', 'Dewi Sartika', 'Hendra Gunawan', 'Rika Permatasari', 'Irfan Maulana',
            'Lina Fitriani', 'Tono Suharto', 'Mega Mustika', 'Arief Rahman', 'Yuli Astuti',
            'Didik Prasetyo', 'Siska Melati', 'Heru Susanto', 'Nita Oktavia', 'Rizky Fadhilah',
            'Eva Nurjanah', 'Cahyono Putro', 'Rina Anggraeni', 'Dimas Ardiansyah', 'Winda Purnama',
        ];

        $furnitureReviews = [
            'Kualitas furniture sangat bagus, material kayu solid dan finishing rapi. Sangat puas dengan pembelian ini.',
            'Produk sesuai dengan deskripsi, pengiriman cepat dan barang sampai dalam kondisi baik. Recommended!',
            'Desain minimalis dan elegan, cocok dengan konsep rumah modern. Terima kasih Bewole Furniture!',
            'Pelayanan memuaskan, produk berkualitas dengan harga yang terjangkau. Akan order lagi untuk kebutuhan lainnya.',
            'Kursinya nyaman banget, cocok untuk working from home. Warnanya juga sesuai dengan gambar.',
            'Meja belajarnya kokoh dan stabil, anak jadi semangat belajar. Desainnya juga cantik.',
            'Sofa yang empuk dan nyaman, bahan kainnya berkualitas. Pengiriman tepat waktu.',
            'Lemari pakaiannya spacious banget, muat banyak baju. Materialnya kuat dan tahan lama.',
            'Ranjangnya nyaman dan kokoh, tidak berisik saat dipakai. Desain minimalis sesuai ekspektasi.',
            'Rak bukunya simple tapi elegan, muat banyak koleksi buku. Sangat memuaskan!',
            'Interior rumah jadi lebih aesthetic setelah pakai furniture dari Bewole. Recommended banget!',
            'Saya beli set meja dan kursi tamu, kualitasnya premium. Tamu-tamu pada suka.',
            'Dekorasinya cantik dan unik, bikin rumah lebih hidup. Pengirimannya cepat dan aman.',
            'Produk custom sesuai dengan ukuran yang saya minta. Hasilnya memuaskan, terima kasih.',
            'Warna furniturnya persis seperti di foto, tidak ada bedanya. Quality control-nya baik.',
            'Dua minggu sudah dipakai, furniturnya masih seperti baru. Kualitasnya memang terjamin.',
            'Proses pemesanan mudah, customer service responsif. Barang sampai sesuai jadwal.',
            'Saya suka banget dengan desainnya yang timeless, tidak akan ketinggalan zaman.',
            'Harganya sebanding dengan kualitas yang didapatkan. Investasi furniture yang worth it.',
            'Bahan yang digunakan benar-benar berkualitas tinggi, terlihat dari detail finishingnya.',
            'Furniture-nya ringan tapi kokoh, mudah dipindahkan kalau mau diubah tata letak ruangan.',
            'Produknya sesuai ekspektasi, bahkan lebih baik dari yang saya bayangkan. Thanks Bewole!',
            'Saya order meja komputer, ukurannya pas dan muat untuk setup dual monitor. Mantap!',
            'Pengiriman ke luar pulau tetap cepat dan barang aman. Packingnya rapi dan tebal.',
            'Dapat diskon besar, kualitas tetap premium. Pasti akan repeat order lagi.',
            'Anak-anak suka dengan rak mainan barunya, warnanya cerah dan bahannya aman.',
            'Saya rekomendasikan Bewole Furniture ke teman-teman. Kualitas dan pelayanan terbaik.',
            'Setelah 1 bulan pemakaian, furniturnya masih terlihat baru. Finishingnya sangat bagus.',
            'Saya suka konsep Scandinavian-nya, minimalis tapi tetap hangat di rumah.',
            'Instruksi perakitan jelas, bisa dirakit sendiri dengan mudah tanpa bantuan tukang.',
            'Saya puas dengan ketahanan furniturnya, tidak mudah rusak meskipun dipakai setiap hari.',
            'Modelnya kekinian banget, bikin ruangan terlihat lebih mewah dan elegan.',
            'Ukurannya pas dengan ruangan saya, tidak terlalu besar dan tidak terlalu kecil.',
            'Pengiriman barang sangat cepat, hanya 2 hari sampai di rumah. Luar biasa!',
            'Furniture ini menjadi pusat perhatian di ruang tamu. Semua tamu memuji.',
            'Saya suka tekstur kayunya yang halus dan natural. Finishing yang sempurna.',
            'Anak saya senang dengan meja belajar barunya, jadi rajin belajar setiap hari.',
            'Pelayanan after sales-nya bagus, cepat tanggap kalau ada pertanyaan.',
            'Kombinasi warnanya netral, cocok dipadukan dengan dekorasi apapun di rumah.',
            'Dari segi harga, ini adalah furniture terbaik yang pernah saya beli. Worth it!',
            'Bau cat atau lem tidak menyengat, aman untuk kesehatan keluarga.',
            'Produk tahan terhadap cuaca lembab, tidak ada masalah meskipun di daerah pantai.',
            'Desain ergonomis, membuat aktivitas sehari-hari jadi lebih nyaman.',
            'Furniture ini multiguna, bisa digunakan untuk berbagai kebutuhan.',
            'Saya sudah 3 kali order di Bewole dan selalu puas dengan hasilnya.',
            'Produk dikirim dengan packing yang sangat aman, tidak ada kerusakan sama sekali.',
            'Warnanya tidak mudah pudar, masih cerah meskipun terkena sinar matahari.',
            'Tidak perlu perawatan khusus, mudah dibersihkan hanya dengan lap basah.',
            'Saya merekomendasikan produk ini untuk yang mencari furniture berkualitas premium.',
            'Bewole Furniture selalu menjadi pilihan utama untuk kebutuhan furniture rumah saya.',
        ];

        $imageSampleCount = 12;

        for ($i = 0; $i < 50; $i++) {
            $userId = $this->getUniqueCombination($usedCombinations, $userIds, $productIds, $orderIds);

            if ($userId === null) {
                $this->command->warn('Ran out of unique user-product-order combinations. Created ' . $createdCount . ' reviews.');
                break;
            }

            $user = User::find($userId);
            $productId = $usedCombinations[count($usedCombinations) - 1]['product_id'];
            $orderId = $usedCombinations[count($usedCombinations) - 1]['order_id'];

            $rating = fake()->randomElement([4, 5]);
            $reviewText = $furnitureReviews[$i % count($furnitureReviews)];
            $createdAt = fake()->dateTimeBetween('-6 months', 'now');

            $review = ProductReview::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'rating' => $rating,
                'review' => $reviewText,
                'is_verified' => true,
                'is_active' => true,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Generate 0–5 review images
            $imageCount = fake()->numberBetween(0, 5);
            for ($j = 0; $j < $imageCount; $j++) {
                $sampleNumber = fake()->numberBetween(1, $imageSampleCount);
                $review->images()->create([
                    'image' => "reviews/review-sample-{$sampleNumber}.jpg",
                ]);
            }

            $createdCount++;
        }

        $this->command->info("Successfully seeded {$createdCount} product reviews with images.");
    }

    /**
     * Get a unique combination of user_id, product_id, and order_id.
     */
    private function getUniqueCombination(
        array &$usedCombinations,
        array $userIds,
        array $productIds,
        array $orderIds
    ): ?int {
        $maxAttempts = 200;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $userId = $userIds[array_rand($userIds)];
            $productId = $productIds[array_rand($productIds)];
            $orderId = $orderIds[array_rand($orderIds)];

            $combinationKey = "{$userId}-{$productId}-{$orderId}";

            if (!isset($usedCombinations[$combinationKey])) {
                $usedCombinations[$combinationKey] = [
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'order_id' => $orderId,
                ];

                return $userId;
            }
        }

        return null;
    }
}

