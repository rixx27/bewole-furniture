<?php

namespace App\Http\Requests\Admin;

use App\Enums\HeroButtonTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHeroBannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize legacy button link values before validation.
     *
     * Old data may contain a full path (e.g. "/products"); this converts
     * those to the canonical dropdown value so backward compatibility is
     * preserved without breaking validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'primary_button_link' => static::normalizeButtonTarget($this->input('primary_button_link')),
            'secondary_button_link' => static::normalizeButtonTarget($this->input('secondary_button_link')),
        ]);
    }

    /**
     * Normalize a single button target value (legacy path -> canonical value).
     */
    protected static function normalizeButtonTarget(?string $value): ?string
    {
        return HeroButtonTarget::fromLegacy($value)?->value ?? $value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
    {
        $targets = array_column(HeroButtonTarget::cases(), 'value');

        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'primary_button_text' => ['required', 'string', 'max:255'],
            'primary_button_link' => ['required', Rule::in($targets)],
            'secondary_button_text' => ['nullable', 'string', 'max:255'],
            'secondary_button_link' => ['nullable', 'string', Rule::in($targets)],
            'text_position' => ['required', 'in:left,center,right'],
            'overlay_opacity' => ['required', 'integer', 'min:0', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
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
            'title.required' => 'Judul hero wajib diisi.',
            'title.max' => 'Judul hero maksimal 255 karakter.',
            'subtitle.required' => 'Sub judul hero wajib diisi.',
            'subtitle.max' => 'Sub judul hero maksimal 255 karakter.',
            'image.image' => 'Gambar latar harus berupa gambar.',
            'image.mimes' => 'Gambar latar harus berformat: jpg, jpeg, png, webp.',
            'image.max' => 'Ukuran gambar latar maksimal 5MB.',
            'badge_text.max' => 'Teks badge maksimal 255 karakter.',
            'primary_button_text.required' => 'Teks tombol utama wajib diisi.',
            'primary_button_text.max' => 'Teks tombol utama maksimal 255 karakter.',
            'primary_button_link.required' => 'Tujuan tombol utama wajib dipilih.',
            'primary_button_link.in' => 'Tujuan tombol utama yang dipilih tidak valid.',
            'secondary_button_text.max' => 'Teks tombol kedua maksimal 255 karakter.',
            'secondary_button_link.in' => 'Tujuan tombol kedua yang dipilih tidak valid.',
            'text_position.required' => 'Posisi teks wajib dipilih.',
            'text_position.in' => 'Posisi teks tidak valid.',
            'overlay_opacity.required' => 'Opacity overlay wajib diisi.',
            'overlay_opacity.integer' => 'Opacity overlay harus berupa angka.',
            'overlay_opacity.min' => 'Opacity overlay minimal 0.',
            'overlay_opacity.max' => 'Opacity overlay maksimal 100.',
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }
}
