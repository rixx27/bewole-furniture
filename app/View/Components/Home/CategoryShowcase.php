<?php

namespace App\View\Components\Home;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CategoryShowcase extends Component
{
    /**
     * Active categories to showcase on the landing page.
     */
    public Collection $categories;

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
     * Base URL segment for the "Explore Collection" links.
     * Defaults to "products" → /products?category={slug}.
     */
    public string $baseUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = 'Explore Our Collection',
        string $badge = 'Kategori Pilihan',
        string $subtitle = 'Temukan furniture premium untuk setiap ruangan di hunian Anda.',
        string $baseUrl = 'produk',
    ) {
        $this->categories = Category::query()
            ->active()
            ->showOnHome()
            ->sorted()
            ->get();

        $this->title = $title;
        $this->badge = $badge;
        $this->subtitle = $subtitle;
        $this->baseUrl = trim($baseUrl, '/');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.home.category-showcase');
    }
}

