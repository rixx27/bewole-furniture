<?php

namespace App\Livewire\Admin\Profile;

use App\Http\Requests\Admin\UpdateCompanyProfileRequest;
use App\Models\CompanyProfile as CompanyProfileModel;
use App\Services\CompanyProfileService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class CompanyProfile extends Component
{
    use WithFileUploads;

    /**
     * The service instance.
     */
    protected CompanyProfileService $profileService;

    /**
     * The profile model instance.
     */
    public ?CompanyProfileModel $profile = null;

    // Section 1: Tentang Kami
    public string $about = '';

    // Section 2: Visi
    public string $vision = '';

    // Section 3: Misi (repeater)
    public array $missions = [];

    // Section 4: Foto Perusahaan (opsional)
    public $company_image = null;
    public ?string $existing_company_image = null;
    public ?string $company_image_preview = null;

    // Section 5: Statistik Perusahaan (manual, angka)
    public string $project_done = '0';
    public string $customers = '0';
    public string $years_established = '0';
    public string $cities_served = '0';

    /**
     * Loading state.
     */
    public bool $isLoading = false;

    /**
     * Boot the component with service.
     */
    public function boot(CompanyProfileService $profileService): void
    {
        $this->profileService = $profileService;
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->profile = $this->profileService->get();

        if ($this->profile) {
            $this->loadProfile();
        }
    }

    /**
     * Load profile data into component properties.
     */
    protected function loadProfile(): void
    {
        $this->about = $this->profile->about ?? '';
        $this->vision = $this->profile->vision ?? '';
        $this->existing_company_image = $this->profile->company_image;

        $this->missions = $this->profile->missions
            ->map(fn ($mission) => ['id' => $mission->id, 'content' => $mission->content])
            ->values()
            ->toArray();

        $stats = $this->profile->statistics->keyBy('title');

        $this->project_done = (string) $this->statValue($stats, 'Project Selesai');
        $this->customers = (string) $this->statValue($stats, 'Pelanggan');
        $this->years_established = (string) $this->statValue($stats, 'Tahun Berdiri');
        $this->cities_served = (string) $this->statValue($stats, 'Kota Terlayani');
    }

    /**
     * Get a single statistic value by title.
     */
    protected function statValue($stats, string $title): int
    {
        $stat = $stats->get($title);

        return $stat ? (int) $stat->manual_value : 0;
    }

    /**
     * Updated hook for company image preview.
     */
    public function updatedCompanyImage(): void
    {
        $this->validateOnly('company_image', [
            'company_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->company_image_preview = $this->company_image->temporaryUrl();
    }

    /**
     * Remove company image.
     */
    public function removeCompanyImage(): void
    {
        // The old file will be cleaned up on the next save.
        $this->existing_company_image = null;
        $this->company_image = null;
        $this->company_image_preview = null;
    }

    // ============ MISSION ============

    /**
     * Add a mission row.
     */
    public function addMission(): void
    {
        $this->missions[] = ['id' => null, 'content' => ''];
    }

    /**
     * Remove a mission row.
     */
    public function removeMission(int $index): void
    {
        unset($this->missions[$index]);
        $this->missions = array_values($this->missions);
    }

    /**
     * Move a mission row up.
     */
    public function moveMissionUp(int $index): void
    {
        if ($index === 0) {
            return;
        }
        $this->moveItem($this->missions, $index, $index - 1);
    }

    /**
     * Move a mission row down.
     */
    public function moveMissionDown(int $index): void
    {
        if ($index >= count($this->missions) - 1) {
            return;
        }
        $this->moveItem($this->missions, $index, $index + 1);
    }

    /**
     * Create the initial profile.
     *
     * Creates a shell record immediately so the form can be shown.
     * The admin then completes the required fields and clicks "Simpan Perubahan".
     */
    public function createProfile(): void
    {
        Gate::authorize('create', CompanyProfileModel::class);

        $this->isLoading = true;

        try {
            $data = [
                'about' => $this->about,
                'vision' => $this->vision,
                'company_image' => $this->company_image,
                'missions' => $this->missions,
                'project_done' => $this->project_done,
                'customers' => $this->customers,
                'years_established' => $this->years_established,
                'cities_served' => $this->cities_served,
            ];

            $this->profile = $this->profileService->create($data);
            $this->loadProfile();
            $this->company_image = null;
            $this->company_image_preview = null;

            $this->dispatch('profile-saved', type: 'success', message: 'Profil perusahaan berhasil dibuat. Silakan lengkapi informasi perusahaan.');
        } catch (\Exception $e) {
            $this->dispatch('profile-saved', type: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Save/update the profile.
     */
    public function save(): void
    {
        Gate::authorize('update', CompanyProfileModel::class);

        $this->isLoading = true;

        try {
            $validated = $this->validateProfile();

            // Handle image upload
            $data = $validated;
            if ($this->company_image) {
                $data['company_image'] = $this->company_image;
            } elseif ($this->existing_company_image === null && $this->profile->company_image) {
                // Image was removed
                $data['remove_company_image'] = true;
            }

            $this->profile = $this->profileService->update($this->profile, $data);
            $this->loadProfile();
            $this->company_image = null;
            $this->company_image_preview = null;

            $this->dispatch('profile-saved', type: 'success', message: 'Profil perusahaan berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('profile-saved', type: 'error', message: 'Validasi gagal. Periksa kembali input Anda.');
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('profile-saved', type: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Validate the profile form.
     */
    protected function validateProfile(): array
    {
        $request = new UpdateCompanyProfileRequest();

        return $this->validate($request->rules(), $request->messages());
    }

    /**
     * Reset the form.
     */
    public function resetForm(): void
    {
        if ($this->profile) {
            $this->loadProfile();
        } else {
            $this->reset([
                'about', 'vision', 'missions',
                'project_done', 'customers', 'years_established', 'cities_served',
            ]);
        }

        $this->company_image = null;
        $this->company_image_preview = null;

        $this->dispatch('profile-saved', type: 'info', message: 'Form telah direset.');
    }

    /**
     * Move a list item to a new position.
     */
    protected function moveItem(array &$items, int $from, int $to): void
    {
        $item = $items[$from];
        $items[$from] = $items[$to];
        $items[$to] = $item;
        $items = array_values($items);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.profile.company-profile', [
            'profile' => $this->profile,
        ]);
    }
}
