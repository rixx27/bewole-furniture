<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductReview>
 */
class ProductReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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
        ];

        $rating = $this->faker->randomElement([4, 5]);

        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
            'rating' => $rating,
            'comment' => $this->faker->randomElement($furnitureReviews),
            'is_verified' => true,
            'is_visible' => true,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * Indicate that the review belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the review belongs to a specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }

    /**
     * Indicate that the review belongs to a specific order.
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $order->id,
        ]);
    }
}

