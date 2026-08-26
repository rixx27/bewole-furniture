<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use Livewire\Component;

class NavbarSearch extends Component
{
    public string $searchQuery = '';

    public function clearSearch()
    {
        $this->searchQuery = '';
    }

    public function searchSubmit()
    {
        if (!empty($this->searchQuery)) {
            return redirect()->route('search.index', ['q' => $this->searchQuery]);
        }
    }

    public function render()
    {
        $results = [];

        if (strlen($this->searchQuery) >= 2) {
            $terms = explode(' ', $this->searchQuery);
            $terms = array_filter($terms, fn($val) => !empty(trim($val)));

            $products = Product::query()
                ->active()
                ->with('category')
                ->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $search = '%' . $term . '%';
                        $q->where(function ($subQ) use ($search) {
                            $subQ->where('name', 'like', $search)
                                ->orWhere('description', 'like', $search)
                                ->orWhere('short_description', 'like', $search)
                                ->orWhere('material', 'like', $search)
                                ->orWhere('sku', 'like', $search)
                                ->orWhereHas('category', function ($categoryQuery) use ($search) {
                                    $categoryQuery->where('name', 'like', $search);
                                });
                        });
                    }
                })
                ->latest()
                ->take(5)
                ->get();

            $results = $products->map(function ($product) {
                return [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->formatted_discount_price ?: $product->formatted_price,
                    'thumbnail' => $product->thumbnail,
                    'category_name' => $product->category?->name ?? 'Bewole'
                ];
            })->toArray();
        }

        return view('livewire.frontend.navbar-search', [
            'results' => $results
        ]);
    }
}
