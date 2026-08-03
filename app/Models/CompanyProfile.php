<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'about',
        'vision',
        'company_image',
    ];

    /**
     * Get the missions for the company profile.
     */
    public function missions(): HasMany
    {
        return $this->hasMany(CompanyMission::class)->orderBy('sort_order');
    }

    /**
     * Get the advantages for the company profile.
     */
    public function advantages(): HasMany
    {
        return $this->hasMany(CompanyAdvantage::class)->orderBy('sort_order');
    }

    /**
     * Get the statistics for the company profile.
     */
    public function statistics(): HasMany
    {
        return $this->hasMany(CompanyStatistic::class)->orderBy('sort_order');
    }

    /**
     * Get the full URL of the company image.
     */
    public function getCompanyImageUrlAttribute(): ?string
    {
        if (!$this->company_image) {
            return null;
        }

        return Storage::url($this->company_image);
    }
}
