<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(OrderStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');
            $newStatus = OrderStatus::tryFrom($this->input('status'));
            if ($order && $newStatus) {
                $currentStatus = OrderStatus::tryFrom($order->status);
                if ($currentStatus && !$currentStatus->canTransitionTo($newStatus)) {
                    $validator->errors()->add('status', 'Status pesanan hanya dapat dilanjutkan ke tahap berikutnya atau dibatalkan.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib dipilih.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status yang dipilih tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
            'description.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
