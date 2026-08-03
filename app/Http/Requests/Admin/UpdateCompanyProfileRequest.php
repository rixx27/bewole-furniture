<?php

namespace App\Http\Requests\Admin;

use App\Models\CompanyStatistic;
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

            // Section 5: Foto Perusahaan
            'company_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Section 3: Misi
            'missions' => ['nullable', 'array'],
            'missions.*.content' => ['required', 'string', 'max:2000'],

            // Section 4: Keunggulan
            'advantages' => ['nullable', 'array'],
            'advantages.*.content' => ['required', 'string', 'max:2000'],

            // Section 6: Statistik
            'statistics' => ['nullable', 'array'],
            'statistics.*.icon' => ['required', 'string', 'max:100'],
            'statistics.*.title' => ['required', 'string', 'max:255'],
            'statistics.*.type' => ['required', 'in:' . CompanyStatistic::TYPE_AUTO . ',' . CompanyStatistic::TYPE_MANUAL],
            'statistics.*.source' => ['nullable', 'required_if:statistics.*.type,auto', 'string', 'max:50'],
            'statistics.*.manual_value' => ['nullable', 'required_if:statistics.*.type,manual', 'string', 'max:255'],
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
            'company_image.image' => 'Foto perusahaan harus berupa gambar.',
            'company_image.mimes' => 'Foto perusahaan harus berformat: jpg, jpeg, png, atau webp.',
            'company_image.max' => 'Foto perusahaan maksimal 2 MB.',
            'missions.*.content.required' => 'Konten misi wajib diisi.',
            'missions.*.content.max' => 'Konten misi maksimal 2000 karakter.',
            'advantages.*.content.required' => 'Konten keunggulan wajib diisi.',
            'advantages.*.content.max' => 'Konten keunggulan maksimal 2000 karakter.',
            'statistics.*.icon.required' => 'Icon statistik wajib diisi.',
            'statistics.*.title.required' => 'Judul statistik wajib diisi.',
            'statistics.*.type.required' => 'Tipe statistik wajib dipilih.',
            'statistics.*.source.required_if' => 'Sumber data wajib dipilih untuk statistik otomatis.',
            'statistics.*.manual_value.required_if' => 'Nilai manual wajib diisi untuk statistik manual.',
        ];
    }
}
