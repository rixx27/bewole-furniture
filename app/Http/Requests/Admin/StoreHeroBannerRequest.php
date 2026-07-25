<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHeroBannerRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'primary_button_text' => ['required', 'string', 'max:255'],
            'primary_button_link' => ['required', 'string', 'max:255'],
            'secondary_button_text' => ['nullable', 'string', 'max:255'],
            'secondary_button_link' => ['nullable', 'string', 'max:255'],
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
            'image.required' => 'Gambar latar wajib diunggah.',
            'image.image' => 'Gambar latar harus berupa gambar.',
            'image.mimes' => 'Gambar latar harus berformat: jpg, jpeg, png, webp.',
            'image.max' => 'Ukuran gambar latar maksimal 5MB.',
            'badge_text.max' => 'Teks badge maksimal 255 karakter.',
            'primary_button_text.required' => 'Teks tombol utama wajib diisi.',
            'primary_button_text.max' => 'Teks tombol utama maksimal 255 karakter.',
            'primary_button_link.required' => 'Link tombol utama wajib diisi.',
            'primary_button_link.max' => 'Link tombol utama maksimal 255 karakter.',
            'secondary_button_text.max' => 'Teks tombol kedua maksimal 255 karakter.',
            'secondary_button_link.max' => 'Link tombol kedua maksimal 255 karakter.',
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

