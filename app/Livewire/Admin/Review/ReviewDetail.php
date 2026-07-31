<?php

namespace App\Livewire\Admin\Review;

use App\Models\ProductReview;
use Livewire\Component;
use Livewire\Attributes\On;

class ReviewDetail extends Component
{
    public ?ProductReview $review = null;
    public bool $show = false;

    #[On('openDetail')]
    public function loadReview(int $reviewId): void
    {
        $this->review = ProductReview::with([
            'user',
            'product',
            'product.images',
            'order',
            'images',
        ])->find($reviewId);

        $this->show = true;
    }

    #[On('closeModal')]
    public function close(): void
    {
        $this->show = false;
        $this->review = null;
    }

    public function render()
    {
        return view('livewire.admin.review.review-detail');
    }
}

