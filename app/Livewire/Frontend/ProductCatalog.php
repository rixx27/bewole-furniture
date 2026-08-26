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

    public string $q = '';
    public string $selectedCategory = '';
    public string $sort = 'latest';

    protected $queryString = [
        'q' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    public function mount(): void
    {
        if (empty($this->selectedCategory)) {
            $this->selectedCategory = (string) (request()->query('selectedCategory') ?: request()->query('category', ''));
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
        $count = $cartService->getItemCount();

        $this->dispatch('cart-updated', count: $count);
        $this->dispatch('notify', message: 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function render()
    {
        $query = Product::query()
            ->active()
            ->with(['category']);

        if (!empty($this->q)) {
            $terms = explode(' ', $this->q);
            $terms = array_filter($terms, fn($val) => !empty(trim($val)));
            
            $query->where(function ($qBuilder) use ($terms) {
                foreach ($terms as $term) {
                    $searchStr = '%' . $term . '%';
                    $qBuilder->where(function ($subQ) use ($searchStr) {
                        $subQ->where('name', 'like', $searchStr)
                             ->orWhere('description', 'like', $searchStr)
                             ->orWhere('short_description', 'like', $searchStr)
                             ->orWhere('material', 'like', $searchStr)
                             ->orWhere('sku', 'like', $searchStr)
                             ->orWhereHas('category', function ($catQ) use ($searchStr) {
                                 $catQ->where('name', 'like', $searchStr);
                             });
                    });
                }
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
