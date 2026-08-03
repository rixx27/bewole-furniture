<?php

namespace App\Livewire\Admin\Profile;

use App\Http\Requests\Admin\UpdateCompanyProfileRequest;
use App\Models\CompanyProfile as CompanyProfileModel;
use App\Models\CompanyStatistic;
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

    // Section 3: Misi
    public array $missions = [];

    // Section 4: Keunggulan
    public array $advantages = [];

    // Section 5: Foto Perusahaan
    public $company_image = null;
    public ?string $existing_company_image = null;
    public ?string $company_image_preview = null;

    // Section 6: Statistik
    public array $statistics = [];

    /**
     * Loading state.
     */
    public bool $isLoading = false;

    /**
     * Available statistic sources.
     */
    public array $statisticSources = [];

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
        $this->statisticSources = CompanyStatistic::SOURCES;

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
            ->map(function ($mission) {
                return ['id' => $mission->id, 'content' => $mission->content];
            })
            ->values()
            ->toArray();

        $this->advantages = $this->profile->advantages
            ->map(function ($advantage) {
                return ['id' => $advantage->id, 'content' => $advantage->content];
            })
            ->values()
            ->toArray();

        $this->statistics = $this->profile->statistics
            ->map(function ($stat) {
                return [
                    'id' => $stat->id,
                    'icon' => $stat->icon,
                    'title' => $stat->title,
                    'type' => $stat->type,
                    'source' => $stat->source,
                    'manual_value' => $stat->manual_value,
                    'is_active' => $stat->is_active,
                ];
            })
            ->values()
            ->toArray();
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

    // ============ ADVANTAGE ============

    /**
     * Add an advantage row.
     */
    public function addAdvantage(): void
    {
        $this->advantages[] = ['id' => null, 'content' => ''];
    }

    /**
     * Remove an advantage row.
     */
    public function removeAdvantage(int $index): void
    {
        unset($this->advantages[$index]);
        $this->advantages = array_values($this->advantages);
    }

    /**
     * Move an advantage row up.
     */
    public function moveAdvantageUp(int $index): void
    {
        if ($index === 0) {
            return;
        }
        $this->moveItem($this->advantages, $index, $index - 1);
    }

    /**
     * Move an advantage row down.
     */
    public function moveAdvantageDown(int $index): void
    {
        if ($index >= count($this->advantages) - 1) {
            return;
        }
        $this->moveItem($this->advantages, $index, $index + 1);
    }

    // ============ STATISTIC ============

    /**
     * Add a statistic row.
     */
    public function addStatistic(): void
    {
        $this->statistics[] = [
            'id' => null,
            'icon' => 'fa-solid fa-chart-line',
            'title' => '',
            'type' => CompanyStatistic::TYPE_AUTO,
            'source' => 'products',
            'manual_value' => '',
            'is_active' => true,
        ];
    }

    /**
     * Remove a statistic row.
     */
    public function removeStatistic(int $index): void
    {
        unset($this->statistics[$index]);
        $this->statistics = array_values($this->statistics);
    }

    /**
     * Move a statistic row up.
     */
    public function moveStatisticUp(int $index): void
    {
        if ($index === 0) {
            return;
        }
        $this->moveItem($this->statistics, $index, $index - 1);
    }

    /**
     * Move a statistic row down.
     */
    public function moveStatisticDown(int $index): void
    {
        if ($index >= count($this->statistics) - 1) {
            return;
        }
        $this->moveItem($this->statistics, $index, $index + 1);
    }

    /**
     * Toggle a statistic active state.
     */
    public function toggleStatistic(int $index): void
    {
        if (isset($this->statistics[$index])) {
            $this->statistics[$index]['is_active'] = !$this->statistics[$index]['is_active'];
        }
    }

    /**
     * Resolve the computed value for a statistic (for display).
     */
    public function resolveStatisticValue(int $index): ?string
    {
        $item = $this->statistics[$index] ?? null;

        if (!$item) {
            return null;
        }

        if (($item['type'] ?? CompanyStatistic::TYPE_AUTO) === CompanyStatistic::TYPE_MANUAL) {
            return $item['manual_value'] ?? '';
        }

        return match ($item['source'] ?? null) {
            'products' => (string) \App\Models\Product::count(),
            'orders' => (string) \App\Models\Order::count(),
            'users' => (string) \App\Models\User::role('user')->count(),
            'reviews' => (string) \App\Models\ProductReview::count(),
            'categories' => (string) \App\Models\Category::count(),
            default => null,
        };
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
                'advantages' => $this->advantages,
                'statistics' => $this->statistics,
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
                'about', 'vision', 'missions', 'advantages', 'statistics',
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
            'statisticSources' => $this->statisticSources,
        ]);
    }
}
