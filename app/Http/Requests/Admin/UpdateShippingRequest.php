<?php

namespace App\Http\Requests\Admin;

use App\Enums\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $shippingMethod = $this->input('shipping_method');

        $rules = [
            'shipping_method' => ['required', new Enum(ShippingMethod::class)],
        ];

        // Dynamic validation based on shipping method
        if ($shippingMethod === ShippingMethod::Expedition->value) {
            $rules['courier'] = ['required', 'string', 'max:255'];
            $rules['tracking_number'] = ['required', 'string', 'max:255'];
            $rules['shipping_date'] = ['required', 'date'];
        } elseif ($shippingMethod === ShippingMethod::InternalDelivery->value) {
            $rules['driver_name'] = ['required', 'string', 'max:255'];
            $rules['vehicle_number'] = ['required', 'string', 'max:255'];
            $rules['shipping_date'] = ['required', 'date'];
        } elseif ($shippingMethod === ShippingMethod::SelfPickup->value) {
            $rules['pickup_date'] = ['required', 'date', 'after_or_equal:today'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'shipping_method.required' => 'Metode pengiriman wajib dipilih.',
            'shipping_method.Illuminate\Validation\Rules\Enum' => 'Metode pengiriman tidak valid.',
            'courier.required' => 'Nama kurir wajib diisi.',
            'courier.max' => 'Nama kurir maksimal 255 karakter.',
            'tracking_number.required' => 'Nomor resi wajib diisi.',
            'tracking_number.max' => 'Nomor resi maksimal 255 karakter.',
            'shipping_date.required' => 'Tanggal kirim wajib diisi.',
            'shipping_date.date' => 'Tanggal kirim tidak valid.',
            'driver_name.required' => 'Nama driver wajib diisi.',
            'driver_name.max' => 'Nama driver maksimal 255 karakter.',
            'vehicle_number.required' => 'Nomor kendaraan wajib diisi.',
            'vehicle_number.max' => 'Nomor kendaraan maksimal 255 karakter.',
            'pickup_date.required' => 'Tanggal pengambilan wajib diisi.',
            'pickup_date.date' => 'Tanggal pengambilan tidak valid.',
            'pickup_date.after_or_equal' => 'Tanggal pengambilan minimal hari ini.',
        ];
    }
}
