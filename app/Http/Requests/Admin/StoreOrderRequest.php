<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'shipping_address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah minimal 1.',
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'customer_phone.required' => 'Nomor telepon wajib diisi.',
            'customer_phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'customer_email.email' => 'Format email tidak valid.',
            'customer_email.max' => 'Email maksimal 255 karakter.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'city.required' => 'Kota wajib diisi.',
            'city.max' => 'Kota maksimal 255 karakter.',
            'postal_code.max' => 'Kode pos maksimal 10 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
