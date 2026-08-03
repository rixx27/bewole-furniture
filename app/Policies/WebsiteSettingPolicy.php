<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsiteSettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WebsiteSetting $websiteSetting): bool
    {
        return false; // No delete allowed
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WebsiteSetting $websiteSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WebsiteSetting $websiteSetting): bool
    {
        return false;
    }
}
