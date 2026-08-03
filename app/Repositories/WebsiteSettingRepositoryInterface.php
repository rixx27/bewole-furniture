<?php

namespace App\Repositories;

use App\Models\WebsiteSetting;

interface WebsiteSettingRepositoryInterface
{
    /**
     * Get the first (and only) website settings record.
     */
    public function get(): ?WebsiteSetting;

    /**
     * Create a new website settings record.
     */
    public function create(array $data): WebsiteSetting;

    /**
     * Update the existing website settings record.
     */
    public function update(WebsiteSetting $settings, array $data): WebsiteSetting;

    /**
     * Check if website settings exist.
     */
    public function exists(): bool;

    /**
     * Delete old file from storage.
     */
    public function deleteOldFile(?string $path): void;
}
