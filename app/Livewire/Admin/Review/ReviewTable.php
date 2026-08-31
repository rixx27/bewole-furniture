<?php

namespace App\Livewire\Admin\Review;

use App\Models\ProductReview;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

class ReviewTable extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $ratingFilter = '';

    #[Url(history: true)]
    public string $visibilityFilter = '';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    public ?int $selectedReviewId = null;
    public bool $showDetailModal = false;

    protected $queryString = ['search', 'ratingFilter', 'visibilityFilter', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRatingFilter()
    {
        $this->resetPage();
    }

    public function updatingVisibilityFilter()
    {
        $this->resetPage();
    }

    protected array $allowedSortFields = [
        'created_at',
        'rating',
        'is_visible',
    ];

    public function sortBy(string $field): void
    {
        if (!in_array($field, $this->allowedSortFields)) {
            $field = 'created_at';
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function openDetail(int $reviewId): void
    {
        $this->selectedReviewId = $reviewId;
        $this->showDetailModal = true;
    }

    #[On('closeModal')]
    public function closeAllModals(): void
    {
        $this->showDetailModal = false;
        $this->selectedReviewId = null;
    }

    #[On('reviewUpdated')]
    public function refreshReviews(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle review visibility with confirmation via SweetAlert2.
     */
    public function toggleVisibility(int $reviewId): void
    {
        $review = ProductReview::findOrFail($reviewId);

        Gate::authorize('toggleVisibility', $review);

        $review->update([
            'is_visible' => !$review->is_visible,
        ]);

        $status = $review->is_visible ? 'ditampilkan' : 'disembunyikan';

        $this->dispatch('reviewUpdated');
        $this->dispatch('notify', type: 'success', message: "Ulasan berhasil {$status}.");
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'ratingFilter', 'visibilityFilter', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $query = ProductReview::with([
            'user',
            'product',
            'product.images',
            'order',
            'images',
        ]);

        // Search by customer name or product name
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('product', function ($productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('comment', 'like', '%' . $this->search . '%');
            });
        }

        // Rating filter
        if ($this->ratingFilter) {
            $query->where('rating', (int) $this->ratingFilter);
        }

        // Visibility filter
        if ($this->visibilityFilter === 'visible') {
            $query->visible();
        } elseif ($this->visibilityFilter === 'hidden') {
            $query->hidden();
        }

        // Sort with validation whitelist
        $sortField = in_array($this->sortField, $this->allowedSortFields) ? $this->sortField : 'created_at';
        $sortDirection = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'desc';
        $query->orderBy($sortField, $sortDirection);

        $reviews = $query->paginate(10);

        // Stats
        $totalReviews = ProductReview::count();
        $averageRating = ProductReview::avg('rating');
        $visibleCount = ProductReview::visible()->count();
        $hiddenCount = ProductReview::hidden()->count();

        return view('livewire.admin.review.review-table', [
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'visibleCount' => $visibleCount,
            'hiddenCount' => $hiddenCount,
        ]);
    }
}

