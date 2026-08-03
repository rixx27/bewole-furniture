<?php

namespace App\Repositories;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingRepository implements WebsiteSettingRepositoryInterface
{
    /**
     * Get the first (and only) website settings record.
     */
    public function get(): ?WebsiteSetting
    {
        return WebsiteSetting::first();
    }

    /**
     * Create a new website settings record.
     */
    public function create(array $data): WebsiteSetting
    {
        return WebsiteSetting::create($data);
    }

    /**
     * Update the existing website settings record.
     */
    public function update(WebsiteSetting $settings, array $data): WebsiteSetting
    {
        $settings->update($data);

        return $settings->fresh();
    }

    /**
     * Check if website settings exist.
     */
    public function exists(): bool
    {
        return WebsiteSetting::exists();
    }

    /**
     * Delete old file from storage.
     */
    public function deleteOldFile(?string $path): void
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
