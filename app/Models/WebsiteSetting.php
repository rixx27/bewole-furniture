<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebsiteSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'website_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // Section 1: Identitas Website
        'logo',
        'site_name',
        'site_tagline',

        // Section 2: Informasi Kontak
        'email',
        'phone',
        'whatsapp',
        'address',
        'google_maps_embed',

        // Section 3: Media Sosial
        'facebook',
        'instagram',
        'tiktok',

        // Section 4: Jam Operasional
        'working_days',
        'working_hours',

        // Section 5: Maintenance Mode
        'is_maintenance',
        'maintenance_message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_maintenance' => 'boolean',
        ];
    }

    /**
     * Get the full URL of the logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return Storage::url($this->logo);
    }

    /**
     * Get the WhatsApp URL with formatted number.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->whatsapp) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        return 'https://wa.me/' . $number;
    }
}

