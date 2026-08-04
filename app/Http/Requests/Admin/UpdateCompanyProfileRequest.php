<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
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
            // Section 1: Tentang Kami
            'about' => ['required', 'string'],

            // Section 2: Visi
            'vision' => ['required', 'string'],

            // Section 3: Misi (repeater - minimal satu)
            'missions' => ['required', 'array', 'min:1'],
            'missions.*.content' => ['required', 'string', 'max:2000'],

            // Section 4: Foto Perusahaan (opsional)
            'company_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Section 5: Statistik Perusahaan (manual, angka)
            'project_done' => ['required', 'integer', 'min:0'],
            'customers' => ['required', 'integer', 'min:0'],
            'years_established' => ['required', 'integer', 'min:0'],
            'cities_served' => ['required', 'integer', 'min:0'],
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
            'about.required' => 'Konten Tentang Kami wajib diisi.',
            'vision.required' => 'Visi wajib diisi.',
            'missions.required' => 'Minimal satu misi wajib ditambahkan.',
            'missions.min' => 'Minimal satu misi wajib ditambahkan.',
            'missions.*.content.required' => 'Konten misi wajib diisi.',
            'missions.*.content.max' => 'Konten misi maksimal 2000 karakter.',
            'company_image.image' => 'Foto perusahaan harus berupa gambar.',
            'company_image.mimes' => 'Foto perusahaan harus berformat: jpg, jpeg, png, atau webp.',
            'company_image.max' => 'Foto perusahaan maksimal 2 MB.',
            'project_done.required' => 'Jumlah Project Selesai wajib diisi.',
            'project_done.integer' => 'Project Selesai harus berupa angka.',
            'customers.required' => 'Jumlah Pelanggan wajib diisi.',
            'customers.integer' => 'Pelanggan harus berupa angka.',
            'years_established.required' => 'Tahun Berdiri wajib diisi.',
            'years_established.integer' => 'Tahun Berdiri harus berupa angka.',
            'cities_served.required' => 'Kota Terlayani wajib diisi.',
            'cities_served.integer' => 'Kota Terlayani harus berupa angka.',
        ];
    }
}
