<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = '';
    public string $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
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

    public function addToCart(int $productId): void
    {
        $cartService = app(CartService::class);
        $cartService->add($productId, 1);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function render()
    {
        $query = Product::query()
            ->active()
            ->with(['category']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->selectedCategory)) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategory);
            });
        }

        match ($this->sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->sorted()->latest(),
        };

        $products = $query->paginate(12);

        $categories = Category::query()
            ->active()
            ->sorted()
            ->get();

        return view('livewire.frontend.product-catalog', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
