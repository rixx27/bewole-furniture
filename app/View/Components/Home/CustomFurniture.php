<?php

namespace App\View\Components\Home;

use App\Helpers\WebsiteSettings;
use App\Models\CompanyProfile;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CustomFurniture extends Component
{
    /**
     * Small badge label above heading.
     */
    public string $badge;

    /**
     * Main headline text.
     */
    public string $heading;

    /**
     * Section description text.
     */
    public string $description;

    /**
     * Primary CTA button label.
     */
    public string $buttonText;

    /**
     * URL for the featured furniture photo.
     */
    public ?string $imageUrl;

    /**
     * Formatted WhatsApp admin phone number (e.g. 6281234567890).
     */
    public ?string $whatsappNumber;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $badge = 'CUSTOM FURNITURE',
        string $heading = 'Punya desain furniture sendiri?',
        string $description = 'Wujudkan furniture sesuai kebutuhan ruang Anda. Dari ukuran, bentuk, hingga detail, konsultasikan kebutuhan furniture Anda bersama Bewole.',
        string $buttonText = 'Request Custom Furniture',
    ) {
        $this->badge = $badge;
        $this->heading = $heading;
        $this->description = $description;
        $this->buttonText = $buttonText;

        // Fetch image URL from existing product thumbnail or company profile image
        $productThumbnail = Product::query()->whereNotNull('thumbnail')->latest()->value('thumbnail');
        if ($productThumbnail) {
            $this->imageUrl = asset('storage/' . $productThumbnail);
        } else {
            $companyImage = CompanyProfile::first()?->company_image;
            $this->imageUrl = $companyImage ? asset('storage/' . $companyImage) : null;
        }

        // Fetch admin whatsapp / phone number from website settings
        $rawWa = WebsiteSettings::get('whatsapp') ?: WebsiteSettings::get('phone');
        if ($rawWa) {
            $cleanNumber = preg_replace('/[^0-9]/', '', (string) $rawWa);
            if (str_starts_with($cleanNumber, '0')) {
                $cleanNumber = '62' . substr($cleanNumber, 1);
            }
            $this->whatsappNumber = $cleanNumber;
        } else {
            $this->whatsappNumber = null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.home.custom-furniture');
    }
}
