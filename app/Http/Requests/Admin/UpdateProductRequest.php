<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $cleanedPrice = str_replace('.', '', $this->input('price'));
            $cleanedPrice = str_replace(',', '.', $cleanedPrice);
            $this->merge(['price' => $cleanedPrice]);
        }

        if ($this->has('price_matang')) {
            $val = $this->input('price_matang');
            if ($val !== null && $val !== '') {
                $cleanedMatang = str_replace('.', '', $val);
                $cleanedMatang = str_replace(',', '.', $cleanedMatang);
                $this->merge(['price_matang' => $cleanedMatang]);
            }
        }

        if ($this->has('weight')) {
            $val = $this->input('weight');
            if ($val !== null && $val !== '') {
                $cleanedWeight = preg_replace('/\s*kg\b/i', '', (string) $val);
                $cleanedWeight = str_replace(',', '.', trim($cleanedWeight));
                $this->merge(['weight' => $cleanedWeight !== '' ? $cleanedWeight : null]);
            }
        }

        if ($this->has('materials') && is_array($this->input('materials'))) {
            $materials = $this->input('materials');
            foreach ($materials as $key => $mat) {
                if (isset($mat['price_per_meter'])) {
                    $cleanedMatPrice = str_replace(['.', ','], ['', '.'], $mat['price_per_meter']);
                    $materials[$key]['price_per_meter'] = $cleanedMatPrice;
                }
            }
            $this->merge(['materials' => $materials]);
        }

        if ($this->has('sort_order')) {
            $val = $this->input('sort_order');
            $this->merge(['sort_order' => $val !== null && $val !== '' ? (int) $val : 0]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'gt:0'],
            'price_matang' => ['nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'material' => ['required', 'string', 'max:255'],
            'seat_material_usage' => ['nullable', 'numeric', 'min:0'],
            'packing_material_usage' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', Rule::in(['active', 'pre_order', 'sold_out'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deleted_images' => ['nullable', 'array'],
            'deleted_images.*' => ['integer', 'exists:product_images,id'],
            'materials' => ['nullable', 'array'],
            'materials.*.type' => ['required', 'in:seat_material,packing_material'],
            'materials.*.name' => ['required', 'string', 'max:255'],
            'materials.*.price_per_meter' => ['required', 'numeric', 'min:0'],
            'materials.*.is_active' => ['nullable', 'boolean'],
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
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'slug.unique' => 'Slug sudah digunakan, silakan gunakan slug lain.',
            'slug.max' => 'Slug maksimal 255 karakter.',
            'short_description.required' => 'Deskripsi singkat wajib diisi.',
            'short_description.max' => 'Deskripsi singkat maksimal 500 karakter.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'price.required' => 'Harga asli wajib diisi.',
            'price.numeric' => 'Harga asli harus berupa angka.',
            'price.gt' => 'Harga asli harus lebih dari 0.',
            'discount_percentage.integer' => 'Diskon harus berupa angka bulat.',
            'discount_percentage.min' => 'Diskon minimal 0%.',
            'discount_percentage.max' => 'Diskon maksimal 100%.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
            'material.required' => 'Bahan wajib diisi.',
            'material.max' => 'Bahan maksimal 255 karakter.',
            'dimensions.max' => 'Dimensi maksimal 255 karakter.',
            'weight.numeric' => 'Berat harus berupa angka (dalam kg).',
            'weight.min' => 'Berat tidak boleh kurang dari 0 kg.',
            'weight.max' => 'Berat maksimal 999.999 kg.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Thumbnail harus berformat: jpg, jpeg, png, webp.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
            'gallery.array' => 'Galeri gambar tidak valid.',
            'gallery.min' => 'Minimal 3 gambar galeri.',
            'gallery.*.required' => 'Setiap gambar galeri wajib diisi.',
            'gallery.*.image' => 'Setiap file galeri harus berupa gambar.',
            'gallery.*.mimes' => 'Gambar galeri harus berformat: jpg, jpeg, png, webp.',
            'gallery.*.max' => 'Ukuran gambar galeri maksimal 2MB per gambar.',
            'deleted_images.*.integer' => 'ID gambar yang dihapus tidak valid.',
            'deleted_images.*.exists' => 'Gambar yang dihapus tidak ditemukan.',
        ];
    }
}

