<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CompanyAdvantage;
use App\Models\CompanyMission;
use App\Models\CompanyProfile;
use App\Models\CompanyStatistic;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
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
            $this->syncAdvantages($profile, $data['advantages'] ?? []);
            $this->syncStatistics($profile, $data['statistics'] ?? []);

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
            $this->syncAdvantages($profile, $data['advantages'] ?? []);
            $this->syncStatistics($profile, $data['statistics'] ?? []);

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
     * Sync the repeatable advantages list.
     */
    protected function syncAdvantages(CompanyProfile $profile, array $advantages): void
    {
        CompanyAdvantage::where('company_profile_id', $profile->id)->delete();

        $items = collect($advantages)
            ->filter(fn ($item) => !empty($item['content']))
            ->values();

        foreach ($items as $index => $item) {
            CompanyAdvantage::create([
                'company_profile_id' => $profile->id,
                'content' => $item['content'],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Sync the repeatable statistics list.
     */
    protected function syncStatistics(CompanyProfile $profile, array $statistics): void
    {
        CompanyStatistic::where('company_profile_id', $profile->id)->delete();

        $items = collect($statistics)
            ->filter(fn ($item) => !empty($item['title']) && !empty($item['icon']))
            ->values();

        foreach ($items as $index => $item) {
            CompanyStatistic::create([
                'company_profile_id' => $profile->id,
                'icon' => $item['icon'],
                'title' => $item['title'],
                'type' => $item['type'] ?? CompanyStatistic::TYPE_AUTO,
                'source' => $item['source'] ?? null,
                'manual_value' => $item['manual_value'] ?? null,
                'sort_order' => $index,
                'is_active' => $item['is_active'] ?? true,
            ]);
        }
    }

    /**
     * Resolve the computed value for a statistic.
     */
    public function resolveStatisticValue(CompanyStatistic $statistic): ?string
    {
        if ($statistic->type === CompanyStatistic::TYPE_MANUAL) {
            return $statistic->manual_value;
        }

        return match ($statistic->source) {
            'products' => (string) Product::count(),
            'orders' => (string) Order::count(),
            'users' => (string) User::role('user')->count(),
            'reviews' => (string) ProductReview::count(),
            'categories' => (string) Category::count(),
            default => null,
        };
    }
}
