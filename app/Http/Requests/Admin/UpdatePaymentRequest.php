<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['required', new Enum(PaymentStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_status.required' => 'Status pembayaran wajib dipilih.',
            'payment_status.Illuminate\Validation\Rules\Enum' => 'Status pembayaran tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
