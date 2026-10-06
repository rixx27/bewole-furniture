<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderCodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomFurnitureController extends Controller
{
    /**
     * Store new custom furniture order request in database.
     */
    public function store(Request $request, OrderCodeGenerator $orderCodeGenerator): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'furniture_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:3000'],
            'dimensions' => ['nullable', 'string', 'max:150'],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120', // max 5MB
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'furniture_type.required' => 'Jenis furniture wajib diisi.',
            'description.required' => 'Deskripsi kebutuhan custom wajib diisi.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal adalah 5MB.',
        ]);

        return DB::transaction(function () use ($request, $orderCodeGenerator) {
            $user = Auth::user();
            $imagePath = null;
            $imageUrl = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('custom-designs', 'public');
                $imageUrl = asset(Storage::url($imagePath));
            }

            $orderCode = $orderCodeGenerator->generateCustom();

            $order = new Order();
            $order->order_code = $orderCode;
            $order->is_custom = true;
            $order->user_id = $user?->id;
            $order->product_id = null;
            $order->customer_name = trim($request->input('name'));
            $order->customer_phone = trim($request->input('whatsapp'));
            $order->whatsapp_number = trim($request->input('whatsapp'));
            $order->customer_email = $user?->email;
            $order->shipping_address = '-';
            $order->city = '-';
            $order->custom_furniture_type = trim($request->input('furniture_type'));
            $order->custom_dimensions = trim((string) $request->input('dimensions')) ?: null;
            $order->custom_design_image = $imagePath;
            $order->notes = trim($request->input('description'));
            $order->customization_details = [
                'is_custom_request' => true,
                'furniture_type' => trim($request->input('furniture_type')),
                'dimensions' => trim((string) $request->input('dimensions')),
                'description' => trim($request->input('description')),
                'image_path' => $imagePath,
                'image_url' => $imageUrl,
            ];
            $order->quantity = 1;
            $order->total_price = 0;
            $order->status = OrderStatus::Pending->value;
            $order->payment_status = PaymentStatus::Unpaid->value;
            $order->payment_method = 'manual_transfer';
            $order->save();

            // Status history awal
            $order->statusHistories()->create([
                'status' => OrderStatus::Pending->value,
                'description' => 'Request custom furniture baru diajukan oleh pelanggan (Menunggu estimasi harga & konfirmasi admin).',
                'changed_by' => $user?->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request custom furniture berhasil disimpan.',
                'order_code' => $order->order_code,
                'order_id' => $order->id,
                'image_url' => $imageUrl,
                'tracking_url' => route('frontend.tracking', ['order_code' => $order->order_code]),
                'orders_url' => route('orders.index'),
                'is_logged_in' => (bool) $user,
            ]);
        });
    }

    /**
     * Upload custom furniture sample/reference design image (legacy/standalone helper).
     */
    public function uploadDesign(Request $request): JsonResponse
    {
        $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120', // max 5MB
            ],
        ], [
            'image.required' => 'File gambar contoh desain harus dipilih.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal adalah 5MB.',
        ]);

        $path = $request->file('image')->store('custom-designs', 'public');
        $url = asset(Storage::url($path));

        return response()->json([
            'success' => true,
            'message' => 'Gambar berhasil diunggah.',
            'path' => $path,
            'url' => $url,
        ]);
    }
}
