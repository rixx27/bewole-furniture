<?php

namespace App\Policies;

use App\Models\User;

class CompanyProfilePolicy
{
    /**
     * Determine whether the user can view any company profiles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the company profile.
     */
    public function view(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create company profiles.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the company profile.
     */
    public function update(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the company profile.
     */
    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
