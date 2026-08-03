<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use App\Repositories\WebsiteSettingRepositoryInterface;
use Illuminate\Http\UploadedFile;

class WebsiteSettingService
{
    /**
     * The repository instance.
     */
    protected WebsiteSettingRepositoryInterface $repository;

    /**
     * Create a new service instance.
     */
    public function __construct(WebsiteSettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the website settings.
     */
    public function get(): ?WebsiteSetting
    {
        return $this->repository->get();
    }

    /**
     * Check if website settings exist.
     */
    public function exists(): bool
    {
        return $this->repository->exists();
    }

    /**
     * Create initial website settings.
     */
    public function create(array $data): WebsiteSetting
    {
        return $this->repository->create($data);
    }

    /**
     * Update website settings with logo upload.
     */
    public function update(WebsiteSetting $settings, array $data): WebsiteSetting
    {
        // Handle Logo Upload
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $this->repository->deleteOldFile($settings->logo);
            $data['logo'] = $data['logo']->store('website/logo', 'public');
        } else {
            unset($data['logo']);
        }

        // Ensure is_maintenance is boolean
        $data['is_maintenance'] = isset($data['is_maintenance']) && $data['is_maintenance'] ? true : false;

        return $this->repository->update($settings, $data);
    }

    /**
     * Get all settings for frontend rendering.
     */
    public function getForFrontend(): array
    {
        $settings = $this->get();

        if (!$settings) {
            return $this->getDefaults();
        }

        return [
            // Section 1: Identitas Website
            'logo' => $settings->logo,
            'logo_url' => $settings->logo_url,
            'site_name' => $settings->site_name ?? config('app.name', 'Bewole Furniture'),
            'site_tagline' => $settings->site_tagline,

            // Section 2: Informasi Kontak
            'email' => $settings->email,
            'phone' => $settings->phone,
            'whatsapp' => $settings->whatsapp,
            'whatsapp_url' => $settings->whatsapp_url,
            'address' => $settings->address,
            'google_maps_embed' => $settings->google_maps_embed,

            // Section 3: Media Sosial
            'facebook' => $settings->facebook,
            'instagram' => $settings->instagram,
            'tiktok' => $settings->tiktok,

            // Section 4: Jam Operasional
            'working_days' => $settings->working_days,
            'working_hours' => $settings->working_hours,

            // Section 5: Maintenance Mode
            'is_maintenance' => $settings->is_maintenance ?? false,
            'maintenance_message' => $settings->maintenance_message,
        ];
    }

    /**
     * Get default settings values.
     */
    protected function getDefaults(): array
    {
        return [
            'logo' => null,
            'logo_url' => null,
            'site_name' => config('app.name', 'Bewole Furniture'),
            'site_tagline' => null,
            'email' => null,
            'phone' => null,
            'whatsapp' => null,
            'whatsapp_url' => null,
            'address' => null,
            'google_maps_embed' => null,
            'facebook' => null,
            'instagram' => null,
            'tiktok' => null,
            'working_days' => null,
            'working_hours' => null,
            'is_maintenance' => false,
            'maintenance_message' => null,
        ];
    }
}

