<?php

namespace App\View\Components\Home;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class FeaturedProducts extends Component
{
    /**
     * Featured/active products to showcase on the landing page.
     */
    public Collection $products;

    /**
     * Section title.
     */
    public string $title;

    /**
     * Small pill label above the title.
     */
    public string $badge;

    /**
     * Supporting paragraph under the title.
     */
    public string $subtitle;

    /**
     * Maximum number of products to display.
     */
    public int $limit;

    /**
     * Base URL segment for the "View Details" links.
     * Defaults to the public catalog page since no per-product
     * public route exists yet.
     */
    public string $baseUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = 'Featured Pieces',
        string $badge = 'Pilihan Unggulan',
        string $subtitle = 'Koleksi pilihan karya furniture unggulan Bewole, dipadukan dengan material premium dan pengerjaan tangan yang elegan.',
        int $limit = 8,
        string $baseUrl = 'catalog',
    ) {
        $this->limit = max(1, $limit);
        $this->title = $title;
        $this->badge = $badge;
        $this->subtitle = $subtitle;
        $this->baseUrl = trim($baseUrl, '/');

        // Primary: featured + active products. Fallback: all active products.
        $this->products = Product::query()
            ->with('category')
            ->active()
            ->sorted()
            ->when(
                Product::query()->active()->featured()->exists(),
                fn ($q) => $q->featured()
            )
            ->limit($this->limit)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.home.featured-products');
    }
}
