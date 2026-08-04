<?php

namespace App\Services;

use App\Models\CompanyMission;
use App\Models\CompanyProfile;
use App\Models\CompanyStatistic;
use App\Repositories\CompanyProfileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CompanyProfileService
{
    /**
     * The repository instance.
     */
    protected CompanyProfileRepositoryInterface $repository;

    /**
     * The four fixed manual statistics.
     */
    protected const STATISTICS = [
        [
            'key' => 'project_done',
            'icon' => 'fa-solid fa-briefcase',
            'title' => 'Project Selesai',
        ],
        [
            'key' => 'customers',
            'icon' => 'fa-solid fa-users',
            'title' => 'Pelanggan',
        ],
        [
            'key' => 'years_established',
            'icon' => 'fa-solid fa-calendar-check',
            'title' => 'Tahun Berdiri',
        ],
        [
            'key' => 'cities_served',
            'icon' => 'fa-solid fa-city',
            'title' => 'Kota Terlayani',
        ],
    ];

    /**
     * Create a new service instance.
     */
    public function __construct(CompanyProfileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the company profile.
     */
    public function get(): ?CompanyProfile
    {
        return $this->repository->get();
    }

    /**
     * Check if a company profile exists.
     */
    public function exists(): bool
    {
        return $this->repository->exists();
    }

    /**
     * Create the company profile with related lists.
     */
    public function create(array $data): CompanyProfile
    {
        return DB::transaction(function () use ($data) {
            $profileData = $this->extractProfileData($data);

            // Handle image upload
            if (isset($profileData['company_image']) && $profileData['company_image'] instanceof UploadedFile) {
                $profileData['company_image'] = $profileData['company_image']->store('company', 'public');
            }

            $profile = $this->repository->create($profileData);

            $this->syncMissions($profile, $data['missions'] ?? []);
            $this->syncStatistics($profile, $data);

            return $this->get();
        });
    }

    /**
     * Update the company profile with related lists.
     */
    public function update(CompanyProfile $profile, array $data): CompanyProfile
    {
        return DB::transaction(function () use ($profile, $data) {
            $profileData = $this->extractProfileData($data);

            // Handle image upload
            if (isset($profileData['company_image']) && $profileData['company_image'] instanceof UploadedFile) {
                $this->repository->deleteOldFile($profile->company_image);
                $profileData['company_image'] = $profileData['company_image']->store('company', 'public');
            } else {
                unset($profileData['company_image']);
            }

            // Handle image removal
            if (!empty($data['remove_company_image'])) {
                $this->repository->deleteOldFile($profile->company_image);
                $profileData['company_image'] = null;
            }

            $this->repository->update($profile, $profileData);

            $this->syncMissions($profile, $data['missions'] ?? []);
            $this->syncStatistics($profile, $data);

            return $this->get();
        });
    }

    /**
     * Extract the main profile fields from the payload.
     */
    protected function extractProfileData(array $data): array
    {
        return [
            'about' => $data['about'] ?? null,
            'vision' => $data['vision'] ?? null,
            'company_image' => $data['company_image'] ?? null,
        ];
    }

    /**
     * Sync the repeatable missions list.
     */
    protected function syncMissions(CompanyProfile $profile, array $missions): void
    {
        CompanyMission::where('company_profile_id', $profile->id)->delete();

        $items = collect($missions)
            ->filter(fn ($item) => !empty($item['content']))
            ->values();

        foreach ($items as $index => $item) {
            CompanyMission::create([
                'company_profile_id' => $profile->id,
                'content' => $item['content'],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Sync the four fixed manual statistics.
     */
    protected function syncStatistics(CompanyProfile $profile, array $data): void
    {
        CompanyStatistic::where('company_profile_id', $profile->id)->delete();

        foreach (static::STATISTICS as $index => $stat) {
            CompanyStatistic::create([
                'company_profile_id' => $profile->id,
                'icon' => $stat['icon'],
                'title' => $stat['title'],
                'type' => CompanyStatistic::TYPE_MANUAL,
                'source' => null,
                'manual_value' => (string) ($data[$stat['key']] ?? 0),
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}
