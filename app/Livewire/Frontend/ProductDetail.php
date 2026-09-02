<?php

namespace App\Livewire\Frontend;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductDetail extends Component
{
    use WithFileUploads;

    public Product $product;
    public int $quantity = 1;
    public ?string $selectedImage = null;

    // Review form properties
    public int $rating = 5;
    public string $comment = '';
    public array $photos = [];

    public function mount(Product $product): void
    {
        $this->product = $product->load([
            'category',
            'images',
            'visibleReviews.user',
            'visibleReviews.images',
        ]);
        $this->selectedImage = $this->product->thumbnail;
    }

    public function incrementQuantity(): void
    {
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function selectImage(string $imagePath): void
    {
        $this->selectedImage = $imagePath;
    }

    public function addToCart(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, $this->quantity);
        $count = $cartService->getItemCount();
        $formattedPrice = $this->product->formatted_discount_price ?: $this->product->formatted_price;

        $this->dispatch('cart-updated', count: $count, product: [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'price' => (int) ($this->product->discount_price ?? $this->product->price),
            'formatted_price' => $formattedPrice,
            'thumbnail' => $this->product->thumbnail ? asset('storage/' . $this->product->thumbnail) : null,
            'quantity' => $this->quantity,
        ]);

        $this->dispatch('notify', 
            message: "{$this->product->name} ({$this->quantity}x) berhasil ditambahkan ke keranjang!",
            type: 'success',
            product_name: $this->product->name,
            product_thumbnail: $this->product->thumbnail ? asset('storage/' . $this->product->thumbnail) : null,
            product_price: $formattedPrice,
            product_quantity: $this->quantity,
            cart_count: $count
        );
    }

    public function buyNow()
    {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, $this->quantity);
        $count = $cartService->getItemCount();
        $formattedPrice = $this->product->formatted_discount_price ?: $this->product->formatted_price;

        $this->dispatch('cart-updated', count: $count, product: [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'price' => (int) ($this->product->discount_price ?? $this->product->price),
            'formatted_price' => $formattedPrice,
            'thumbnail' => $this->product->thumbnail ? asset('storage/' . $this->product->thumbnail) : null,
            'quantity' => $this->quantity,
        ]);

        if (!Auth::check()) {
            session(['url.intended' => route('checkout.index')]);
            return redirect()->route('login');
        }

        return redirect()->route('checkout.index');
    }

    public function removePhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function getCanReviewProperty(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Order::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->where('status', OrderStatus::Completed->value)
            ->whereDoesntHave('review')
            ->exists();
    }

    public function getHasReviewedProperty(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return ProductReview::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->exists();
    }

    public function submitReview(): void
    {
        if (!Auth::check()) {
            session()->flash('review_error', 'Silakan login terlebih dahulu untuk memberikan ulasan.');
            return;
        }

        $user = Auth::user();

        $eligibleOrder = Order::where('user_id', $user->id)
            ->where('product_id', $this->product->id)
            ->where('status', OrderStatus::Completed->value)
            ->whereDoesntHave('review')
            ->latest()
            ->first();

        if (!$eligibleOrder) {
            $hasReviewed = ProductReview::where('user_id', $user->id)
                ->where('product_id', $this->product->id)
                ->exists();

            if ($hasReviewed) {
                session()->flash('review_error', 'Anda sudah memberikan ulasan untuk produk ini.');
            } else {
                session()->flash('review_error', 'Anda hanya dapat memberikan ulasan untuk produk yang sudah Anda beli dan pesanannya telah selesai.');
            }
            return;
        }

        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
            'photos.max' => 'Maksimal 5 foto ulasan.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran file gambar maksimal 2MB per foto.',
        ]);

        DB::transaction(function () use ($user, $eligibleOrder) {
            $review = ProductReview::create([
                'user_id' => $user->id,
                'product_id' => $this->product->id,
                'order_id' => $eligibleOrder->id,
                'rating' => $this->rating,
                'comment' => $this->comment ?: null,
                'is_verified' => true,
                'is_visible' => false,
            ]);

            if (!empty($this->photos)) {
                foreach ($this->photos as $photo) {
                    $path = $photo->store('reviews', 'public');
                    $review->images()->create([
                        'image' => $path,
                    ]);
                }
            }
        });

        $this->reset(['rating', 'comment', 'photos']);
        $this->rating = 5;

        session()->flash('review_success', 'Terima kasih! Ulasan Anda telah berhasil dikirim dan sedang menunggu persetujuan admin.');
        $this->dispatch('notify', message: 'Ulasan Anda berhasil dikirim dan menunggu persetujuan admin.');
        $this->product->load([
            'category',
            'images',
            'visibleReviews.user',
            'visibleReviews.images',
        ]);
    }

    public function render()
    {
        return view('livewire.frontend.product-detail');
    }
}

