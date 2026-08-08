<?php

namespace App\View\Components\Home;

use App\Models\CompanyProfile;
use App\Services\CompanyProfileService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Philosophy extends Component
{
    /**
     * The company profile used for the "Our Philosophy" preview.
     */
    public ?CompanyProfile $profile;

    /**
     * Section small label (pill above heading).
     */
    public string $badge;

    /**
     * Section headline.
     */
    public string $heading;

    /**
     * Button label that routes to the Tentang Kami page.
     */
    public string $buttonText;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $badge = 'Our Philosophy',
        string $heading = 'Crafted with intention.',
        string $buttonText = 'Selengkapnya',
    ) {
        $this->profile = app(CompanyProfileService::class)->get();
        $this->badge = $badge;
        $this->heading = $heading;
        $this->buttonText = $buttonText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.home.philosophy');
    }
}
