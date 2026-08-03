<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyMission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_missions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_profile_id',
        'content',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the company profile that owns the mission.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }
}
