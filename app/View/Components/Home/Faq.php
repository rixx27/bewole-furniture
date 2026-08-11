<?php

namespace App\View\Components\Home;

use App\Models\Faq as FaqModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class Faq extends Component
{
    /**
     * Active FAQ items fetched dynamically from database.
     */
    public Collection $faqs;

    /**
     * Small uppercase badge text.
     */
    public string $badge;

    /**
     * Main section title.
     */
    public string $heading;

    /**
     * Short section description text.
     */
    public string $description;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $badge = 'FREQUENTLY ASKED QUESTIONS',
        string $heading = 'Pertanyaan yang Sering Diajukan',
        string $description = 'Temukan jawaban untuk pertanyaan yang sering ditanyakan tentang produk, pemesanan, dan layanan Bewole.',
    ) {
        $this->badge = $badge;
        $this->heading = $heading;
        $this->description = $description;

        // Fetch active FAQs sorted by sort_order ascending, then latest created
        $this->faqs = FaqModel::query()
            ->active()
            ->sorted()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.home.faq');
    }
}
