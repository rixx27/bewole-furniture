<?php

namespace App\Repositories;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Storage;

class CompanyProfileRepository implements CompanyProfileRepositoryInterface
{
    /**
     * Get the first (and only) company profile record.
     */
    public function get(): ?CompanyProfile
    {
        return CompanyProfile::with(['missions', 'advantages', 'statistics'])->first();
    }

    /**
     * Create a new company profile record.
     */
    public function create(array $data): CompanyProfile
    {
        return CompanyProfile::create($data);
    }

    /**
     * Update the existing company profile record.
     */
    public function update(CompanyProfile $profile, array $data): CompanyProfile
    {
        $profile->update($data);

        return $profile->fresh();
    }

    /**
     * Check if a company profile exists.
     */
    public function exists(): bool
    {
        return CompanyProfile::exists();
    }

    /**
     * Delete an old file from storage.
     */
    public function deleteOldFile(?string $path): void
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
