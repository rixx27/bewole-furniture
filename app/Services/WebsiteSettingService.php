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
            $this->syncFavicons(storage_path('app/public/' . $data['logo']));
        } else {
            unset($data['logo']);
        }

        // Handle Custom Furniture Image Upload / Removal
        if (isset($data['custom_furniture_image']) && $data['custom_furniture_image'] instanceof UploadedFile) {
            $this->repository->deleteOldFile($settings->custom_furniture_image);
            $data['custom_furniture_image'] = $data['custom_furniture_image']->store('website/custom-furniture', 'public');
        } elseif (!empty($data['remove_custom_furniture_image'])) {
            $this->repository->deleteOldFile($settings->custom_furniture_image);
            $data['custom_furniture_image'] = null;
            unset($data['remove_custom_furniture_image']);
        } else {
            unset($data['custom_furniture_image']);
            unset($data['remove_custom_furniture_image']);
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
            'custom_furniture_image' => $settings->custom_furniture_image,
            'custom_furniture_image_url' => $settings->custom_furniture_image_url,

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

            // Section 6: Branding
            'login_background' => $settings->login_background,
            'login_quote' => $settings->login_quote,

            // Section 7: SEO & Metadata
            'meta_title' => $settings->meta_title,
            'meta_description' => $settings->meta_description,
            'meta_keywords' => $settings->meta_keywords,
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
            'custom_furniture_image' => null,
            'custom_furniture_image_url' => null,
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
            'login_background' => null,
            'login_quote' => null,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
        ];
    }

    /**
     * Synchronize public favicons from logo image.
     */
    public function syncFavicons(string $sourcePath): void
    {
        if (!file_exists($sourcePath) || !extension_loaded('gd')) {
            return;
        }

        try {
            $imageInfo = @getimagesize($sourcePath);
            if (!$imageInfo) {
                return;
            }

            $mime = $imageInfo['mime'];
            $src = match ($mime) {
                'image/png' => @imagecreatefrompng($sourcePath),
                'image/jpeg' => @imagecreatefromjpeg($sourcePath),
                'image/webp' => @imagecreatefromwebp($sourcePath),
                default => null,
            };

            if (!$src) {
                return;
            }

            $w = imagesx($src);
            $h = imagesy($src);

            // Calculate square crop based on center
            $size = min($w, $h);
            $cropX = (int) max(0, ($w - $size) / 2);
            $cropY = (int) max(0, ($h - $size) / 2);

            $master = imagecreatetruecolor(512, 512);
            imagealphablending($master, false);
            imagesavealpha($master, true);
            $transparent = imagecolorallocatealpha($master, 255, 255, 255, 127);
            imagefill($master, 0, 0, $transparent);
            imagecopyresampled($master, $src, 0, 0, $cropX, $cropY, 512, 512, $size, $size);

            // 1. apple-touch-icon.png (180x180)
            $apple = imagecreatetruecolor(180, 180);
            imagealphablending($apple, false);
            imagesavealpha($apple, true);
            imagefill($apple, 0, 0, $transparent);
            imagecopyresampled($apple, $master, 0, 0, 0, 0, 180, 180, 512, 512);
            imagepng($apple, public_path('apple-touch-icon.png'));
            imagedestroy($apple);

            // 2. favicon.ico (multi-res 16, 32, 48, 64, 128)
            $sizes = [16, 32, 48, 64, 128];
            $icoPngList = [];
            foreach ($sizes as $s) {
                $img = imagecreatetruecolor($s, $s);
                imagealphablending($img, false);
                imagesavealpha($img, true);
                imagefill($img, 0, 0, $transparent);
                imagecopyresampled($img, $master, 0, 0, 0, 0, $s, $s, 512, 512);

                ob_start();
                imagepng($img);
                $icoPngList[] = ['size' => $s, 'data' => ob_get_clean()];
                if ($s === 32) {
                    imagepng($img, public_path('favicon-32x32.png'));
                }
                if ($s === 16) {
                    imagepng($img, public_path('favicon-16x16.png'));
                }
                imagedestroy($img);
            }

            // Pack ICO format
            $count = count($icoPngList);
            $header = pack('v3', 0, 1, $count);
            $offset = 6 + ($count * 16);
            $dirEntries = '';
            $dataSection = '';
            foreach ($icoPngList as $item) {
                $s = $item['size'];
                $data = $item['data'];
                $len = strlen($data);
                $width = ($s >= 256) ? 0 : $s;
                $height = ($s >= 256) ? 0 : $s;
                $dirEntries .= pack('CCCCvvVV', $width, $height, 0, 0, 1, 32, $len, $offset);
                $dataSection .= $data;
                $offset += $len;
            }
            file_put_contents(public_path('favicon.ico'), $header . $dirEntries . $dataSection);

            // 3. favicon.svg
            ob_start();
            imagepng($master);
            $masterPng = ob_get_clean();
            $base64 = base64_encode($masterPng);
            $svgContent = "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" width=\"100%\" height=\"100%\">\n  <image width=\"512\" height=\"512\" href=\"data:image/png;base64,{$base64}\" />\n</svg>";
            file_put_contents(public_path('favicon.svg'), $svgContent);

            imagedestroy($master);
            imagedestroy($src);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to sync favicons: ' . $e->getMessage());
        }
    }
}

