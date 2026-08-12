<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Section 1: Identitas Website
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],

            // Section 2: Informasi Kontak
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'address' => ['nullable', 'string'],
            'google_maps_embed' => ['nullable', 'string'],

            // Section 3: Media Sosial
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],

            // Section 4: Jam Operasional
            'working_days' => ['nullable', 'string', 'max:255'],
            'working_hours' => ['nullable', 'string', 'max:255'],

            // Section 5: Maintenance Mode
            'is_maintenance' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string'],

            // Section 6: Branding
            'login_background' => ['nullable', 'string', 'max:255'],
            'login_quote' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'site_name.max' => 'Nama website maksimal 255 karakter.',
            'site_tagline.max' => 'Tagline maksimal 255 karakter.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat: jpg, jpeg, png, svg, atau webp.',
            'logo.max' => 'Logo maksimal 2 MB.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
            'whatsapp.max' => 'Nomor WhatsApp maksimal 50 karakter.',
        ];
    }
}

