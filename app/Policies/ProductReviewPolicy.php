<?php

namespace App\Policies;

use App\Models\ProductReview;
use App\Models\User;

class ProductReviewPolicy
{
    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, ProductReview $productReview): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can toggle visibility of the review.
     */
    public function toggleVisibility(User $user, ProductReview $productReview): bool
    {
        return $user->hasRole('admin');
    }
}

