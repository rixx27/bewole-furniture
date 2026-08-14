<?php

namespace App\Livewire\Admin\Settings;

use App\Http\Requests\Admin\UpdateWebsiteSettingRequest;
use App\Models\WebsiteSetting;
use App\Services\WebsiteSettingService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class WebsiteSettings extends Component
{
    use WithFileUploads;

    #[Url(as: 'tab', history: true)]
    public string $activeTab = 'info';

    /**
     * The service instance.
     */
    protected WebsiteSettingService $settingService;

    /**
     * The settings model instance.
     */
    public ?WebsiteSetting $settings = null;

    // Section 1: Identitas Website
    public $logo = null;
    public $existing_logo = null;
    public string $site_name = '';
    public string $site_tagline = '';

    // Section 2: Informasi Kontak
    public string $email = '';
    public string $phone = '';
    public string $whatsapp = '';
    public string $address = '';
    public string $google_maps_embed = '';

    // Section 3: Media Sosial
    public string $facebook = '';
    public string $instagram = '';
    public string $tiktok = '';

    // Section 4: Jam Operasional
    public string $working_days = '';
    public string $working_hours = '';

    // Section 5: Maintenance Mode
    public bool $is_maintenance = false;
    public string $maintenance_message = '';

    // Section 6: Branding (Login Page)
    public string $login_background = '';
    public string $login_quote = '';

    /**
     * Loading state.
     */
    public bool $isLoading = false;

    /**
     * Temporary logo preview URL.
     */
    public ?string $logo_preview = null;

    /**
     * Boot the component with service.
     */
    public function boot(WebsiteSettingService $settingService): void
    {
        $this->settingService = $settingService;
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->settings = $this->settingService->get();

        if ($this->settings) {
            $this->loadSettings();
        }
    }

    /**
     * Switch current active tab.
     */
    public function setTab(string $tab): void
    {
        if (in_array($tab, ['info', 'contact', 'seo', 'system'])) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Load settings data into component properties.
     */
    protected function loadSettings(): void
    {
        $this->existing_logo = $this->settings->logo;
        $this->site_name = $this->settings->site_name ?? '';
        $this->site_tagline = $this->settings->site_tagline ?? '';

        $this->email = $this->settings->email ?? '';
        $this->phone = $this->settings->phone ?? '';
        $this->whatsapp = $this->settings->whatsapp ?? '';
        $this->address = $this->settings->address ?? '';
        $this->google_maps_embed = $this->settings->google_maps_embed ?? '';

        $this->facebook = $this->settings->facebook ?? '';
        $this->instagram = $this->settings->instagram ?? '';
        $this->tiktok = $this->settings->tiktok ?? '';

        $this->working_days = $this->settings->working_days ?? '';
        $this->working_hours = $this->settings->working_hours ?? '';

        $this->is_maintenance = $this->settings->is_maintenance ?? false;
        $this->maintenance_message = $this->settings->maintenance_message ?? '';

        $this->login_background = $this->settings->login_background ?? '';
        $this->login_quote = $this->settings->login_quote ?? '';
    }

    /**
     * Updated hook for logo preview.
     */
    public function updatedLogo(): void
    {
        $this->validateOnly('logo', [
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
        ]);

        $this->logo_preview = $this->logo->temporaryUrl();
    }

    /**
     * Remove logo.
     */
    public function removeLogo(): void
    {
        if ($this->existing_logo) {
            Storage::disk('public')->delete($this->existing_logo);
        }
        $this->logo = null;
        $this->existing_logo = null;
        $this->logo_preview = null;
    }

    /**
     * Create initial settings.
     */
    public function createSettings(): void
    {
        $this->isLoading = true;

        try {
            $data = $this->formData();
            $this->settingService->create($data);
            $this->settings = $this->settingService->get();

            $this->dispatch('settings-saved', type: 'success', message: 'Pengaturan website berhasil dibuat.');
        } catch (\Exception $e) {
            $this->dispatch('settings-saved', type: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Save/update settings.
     */
    public function save(): void
    {
        $this->isLoading = true;

        try {
            // Validate
            $request = new UpdateWebsiteSettingRequest();
            $validated = $this->validate($request->rules(), $request->messages());

            if ($this->settings) {
                // Update existing
                $this->settingService->update($this->settings, $validated);
                $this->settings = $this->settingService->get();
                $this->loadSettings();
                $this->dispatch('settings-saved', type: 'success', message: 'Pengaturan website berhasil diperbarui.');
            } else {
                // Create new
                $this->settingService->create($validated);
                $this->settings = $this->settingService->get();
                $this->loadSettings();
                $this->dispatch('settings-saved', type: 'success', message: 'Pengaturan website berhasil dibuat.');
            }

            // Reset previews
            $this->logo_preview = null;
            $this->logo = null;

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('settings-saved', type: 'error', message: 'Validasi gagal. Periksa kembali input Anda.');
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('settings-saved', type: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Reset the form.
     */
    public function resetForm(): void
    {
        if ($this->settings) {
            $this->loadSettings();
        } else {
            $this->reset([
                'site_name', 'site_tagline', 'logo',
                'email', 'phone', 'whatsapp', 'address', 'google_maps_embed',
                'facebook', 'instagram', 'tiktok',
                'working_days', 'working_hours',
                'is_maintenance', 'maintenance_message',
                'login_background', 'login_quote',
            ]);
        }

        $this->logo_preview = null;
        $this->logo = null;

        $this->dispatch('settings-saved', type: 'info', message: 'Form telah direset.');
    }

    /**
     * Get form data as array.
     */
    protected function formData(): array
    {
        return [
            'logo' => $this->logo,
            'site_name' => $this->site_name,
            'site_tagline' => $this->site_tagline,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'google_maps_embed' => $this->google_maps_embed,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
            'working_days' => $this->working_days,
            'working_hours' => $this->working_hours,
            'is_maintenance' => $this->is_maintenance,
            'maintenance_message' => $this->maintenance_message,
            'login_background' => $this->login_background,
            'login_quote' => $this->login_quote,
        ];
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.settings.website-settings', [
            'settings' => $this->settings,
        ]);
    }
}

