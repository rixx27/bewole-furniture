<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use App\Services\ProductSearchService;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $q = '';
    public string $selectedCategory = '';
    public string $selectedMaterial = '';
    public string $sort = 'latest';

    protected $queryString = [
        'q' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedMaterial' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    public function mount(): void
    {
        if (empty($this->selectedCategory)) {
            $this->selectedCategory = (string) (request()->query('selectedCategory') ?: request()->query('category', ''));
        }
        if (empty($this->selectedMaterial)) {
            $this->selectedMaterial = (string) request()->query('material', '');
        }
        if (empty($this->q)) {
            $this->q = (string) request()->query('q', '');
        }
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedMaterial(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function selectCategory(string $slug = ''): void
    {
        $this->selectedCategory = $slug;
        $this->resetPage();
    }

    public function selectMaterial(string $material = ''): void
    {
        $this->selectedMaterial = $material;
        $this->resetPage();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::active()->with('category')->find($productId);
        if (!$product) {
            $this->dispatch('notify', message: 'Produk tidak ditemukan atau tidak tersedia.', type: 'error');
            return;
        }

        $cartService = app(CartService::class);
        $cartService->add($productId, 1);
        $count = $cartService->getItemCount();
        $formattedPrice = $product->formatted_discount_price ?: $product->formatted_price;

        $this->dispatch('cart-updated', count: $count, product: [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (int) ($product->discount_price ?? $product->price),
            'formatted_price' => $formattedPrice,
            'thumbnail' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
            'quantity' => 1,
        ]);

        $this->dispatch('notify', 
            message: "{$product->name} berhasil ditambahkan ke keranjang!",
            type: 'success',
            product_name: $product->name,
            product_thumbnail: $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
            product_price: $formattedPrice,
            product_quantity: 1,
            cart_count: $count
        );
    }

    public function render()
    {
        $query = Product::query()
            ->active()
            ->with(['category']);

        $searchMeta = [
            'matching_categories' => collect(),
            'detected_materials' => [],
        ];

        if (!empty($this->q)) {
            $searchService = app(ProductSearchService::class);
            $searchMeta = $searchService->applySearch($query, $this->q, $this->sort);
        }

        if (!empty($this->selectedCategory)) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategory);
            });
        }

        if (!empty($this->selectedMaterial)) {
            $query->where('products.material', 'like', '%' . $this->selectedMaterial . '%');
        }

        // Apply fallback or user-selected sorting
        if (empty($this->q) || $this->sort !== 'latest') {
            match ($this->sort) {
                'price_low' => $query->orderBy('price', 'asc'),
                'price_high' => $query->orderBy('price', 'desc'),
                'name' => $query->orderBy('name', 'asc'),
                default => $query->sorted()->latest(),
            };
        }

        $products = $query->paginate(12);

        $categories = Category::query()
            ->active()
            ->sorted()
            ->get();

        return view('livewire.frontend.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'matchingCategories' => $searchMeta['matching_categories'] ?? collect(),
            'detectedMaterials' => $searchMeta['detected_materials'] ?? [],
        ]);
    }
}
