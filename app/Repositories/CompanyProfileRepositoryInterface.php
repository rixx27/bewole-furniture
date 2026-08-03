<?php

namespace App\Repositories;

use App\Models\CompanyProfile;

interface CompanyProfileRepositoryInterface
{
    /**
     * Get the first (and only) company profile record.
     */
    public function get(): ?CompanyProfile;

    /**
     * Create a new company profile record.
     */
    public function create(array $data): CompanyProfile;

    /**
     * Update the existing company profile record.
     */
    public function update(CompanyProfile $profile, array $data): CompanyProfile;

    /**
     * Check if a company profile exists.
     */
    public function exists(): bool;

    /**
     * Delete an old file from storage.
     */
    public function deleteOldFile(?string $path): void;
}
