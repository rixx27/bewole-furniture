<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Helpers\WebsiteSettings;
use App\Models\Order;
use App\Models\WhatsAppNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.whatsapp.url', env('WHATSAPP_API_URL', '')), '/');
        $this->apiKey = config('services.whatsapp.key', env('WHATSAPP_API_KEY', ''));
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function isEnabled(): bool
    {
        return (bool) WebsiteSettings::get('whatsapp_notification_enabled', true);
    }

    public function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $raw = trim($phone);
        $hasPlus = str_starts_with($raw, '+');

        $cleaned = preg_replace('/[^0-9]/', '', $raw);

        if (empty($cleaned)) {
            return null;
        }

        // 1. Prefix 00 (international call prefix, misal 0060...)
        if (str_starts_with($cleaned, '00')) {
            $cleaned = substr($cleaned, 2);
        }
        // 2. Jika user secara eksplisit menyertakan tanda '+' (nomor internasional)
        elseif ($hasPlus) {
            // Sudah memiliki kode negara, biarkan nomor apa adanya
        }
        // 3. Format lokal Indonesia (08...)
        elseif (str_starts_with($cleaned, '08')) {
            $cleaned = '62' . substr($cleaned, 1);
        }
        // 4. Nomor lokal Indonesia yang diketik tanpa angka 0 di depan (812...)
        elseif (str_starts_with($cleaned, '8') && strlen($cleaned) >= 9 && strlen($cleaned) <= 13) {
            $cleaned = '62' . $cleaned;
        }

        // Standar E.164 nomor WhatsApp internasional (8 sampai 16 digit)
        if (strlen($cleaned) < 8 || strlen($cleaned) > 16) {
            return null;
        }

        return $cleaned;
    }

    public function sendOrderStatusNotification(Order $order, OrderStatus $newStatus): bool
    {
        if (!$this->isEnabled()) {
            Log::info("WA Notif dilewati (dinonaktifkan oleh admin): Order {$order->order_code} status {$newStatus->value}");
            return false;
        }

        $notifiableStatuses = [
            OrderStatus::PaymentReceived->value,
            OrderStatus::InProduction->value,
            OrderStatus::QualityControl->value,
            OrderStatus::ReadyToShip->value,
            OrderStatus::Shipped->value,
        ];

        if (!in_array($newStatus->value, $notifiableStatuses)) {
            return false;
        }

        $alreadySent = WhatsAppNotificationLog::where('order_id', $order->id)
            ->where('status_trigger', $newStatus->value)
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            Log::info("WA Notif dilewati (sudah terkirim): Order {$order->order_code} status {$newStatus->value}");
            return true;
        }

        $rawPhone = $order->whatsapp_number ?: $order->customer_phone;
        $normalizedPhone = $this->normalizePhoneNumber($rawPhone);

        if (!$normalizedPhone) {
            Log::warning("WA Notif gagal: Nomor WhatsApp order {$order->order_code} tidak valid ({$rawPhone})");
            return false;
        }

        $message = $this->composeStatusMessage($order, $newStatus);

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(12)
                ->post("{$this->apiUrl}/api/send-message", [
                    'phone' => $normalizedPhone,
                    'message' => $message,
                ]);

            $isSuccess = $response->successful() && ($response->json('success') === true);

            WhatsAppNotificationLog::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'status_trigger' => $newStatus->value,
                ],
                [
                    'order_code' => $order->order_code,
                    'recipient_phone' => $normalizedPhone,
                    'message' => $message,
                    'status' => $isSuccess ? 'sent' : 'failed',
                    'error_message' => $isSuccess ? null : ($response->body() ?: 'HTTP Error'),
                ]
            );

            return $isSuccess;
        } catch (\Throwable $e) {
            Log::error("WA Microservice Error: " . $e->getMessage());

            WhatsAppNotificationLog::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'status_trigger' => $newStatus->value,
                ],
                [
                    'order_code' => $order->order_code,
                    'recipient_phone' => $normalizedPhone,
                    'message' => $message,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]
            );

            return false;
        }
    }

    protected function isInternationalPhone(?string $phone): bool
    {
        $normalized = $this->normalizePhoneNumber($phone);
        return $normalized !== null && !str_starts_with($normalized, '62');
    }

    protected function composeStatusMessage(Order $order, OrderStatus $status): string
    {
        $rawPhone = $order->whatsapp_number ?: $order->customer_phone;
        if ($this->isInternationalPhone($rawPhone)) {
            return $this->composeEnglishStatusMessage($order, $status);
        }

        $customer = $order->customer_name ?: 'Pelanggan';
        $trackingUrl = route('frontend.tracking', ['order_code' => $order->order_code]);

        $statusText = match ($status) {
            OrderStatus::PaymentReceived => "Pembayaran pesanan Anda telah berhasil kami terima dan diverifikasi.",
            OrderStatus::InProduction => "Pesanan furniture Anda saat ini telah masuk ke tahap proses produksi pengerjaan.",
            OrderStatus::QualityControl => "Pengerjaan furniture telah selesai dan sedang dalam tahap pemeriksaan kualitas (Quality Control).",
            OrderStatus::ReadyToShip => "Pesanan Anda telah lolos pemeriksaan kualitas dan siap untuk dikirimkan.",
            OrderStatus::Shipped => "Pesanan Anda telah dikirimkan via " . ($order->shipping_method_label ?? 'ekspedisi') . ($order->tracking_number ? " dengan nomor resi: *{$order->tracking_number}*" : "") . ".",
            default => "Status pesanan Anda telah diperbarui menjadi: " . $status->label(),
        };

        return "Halo, Kak {$customer},\n\n"
            . "Pemberitahuan dari Admin *Bewole Jepara Furniture*:\n"
            . "Nomor Pesanan: *#{$order->order_code}*\n"
            . "Status Terbaru: *{$status->label()}*\n\n"
            . "{$statusText}\n\n"
            . "Periksa perkembangan pesanan Anda melalui tautan berikut:\n"
            . "{$trackingUrl}\n\n"
            . "Terima kasih atas kepercayaan Anda kepada Bewole Jepara Furniture🙏.";
    }

    protected function composeEnglishStatusMessage(Order $order, OrderStatus $status): string
    {
        $customer = $order->customer_name ?: 'Customer';
        $trackingUrl = route('frontend.tracking', ['order_code' => $order->order_code]);

        $statusLabel = match ($status) {
            OrderStatus::PaymentReceived => "Payment Verified",
            OrderStatus::InProduction => "In Production",
            OrderStatus::QualityControl => "Quality Control",
            OrderStatus::ReadyToShip => "Ready to Ship",
            OrderStatus::Shipped => "Shipped",
            default => $status->label(),
        };

        $statusText = match ($status) {
            OrderStatus::PaymentReceived => "We have successfully received and verified your order payment.",
            OrderStatus::InProduction => "Your furniture is currently in the production process by our skilled craftsmen.",
            OrderStatus::QualityControl => "Crafting is completed and your items are now undergoing Quality Control (QC).",
            OrderStatus::ReadyToShip => "Your order has passed quality inspection and is packed, ready to be dispatched.",
            OrderStatus::Shipped => "Your order has been dispatched via " . ($order->shipping_method_label ?? 'freight service') . ($order->tracking_number ? " with tracking number: *{$order->tracking_number}*" : "") . ".",
            default => "Your order status has been updated to: " . $statusLabel,
        };

        return "Hello, {$customer},\n\n"
            . "Order Update from *Bewole Jepara Furniture*:\n"
            . "Order Number: *#{$order->order_code}*\n"
            . "Latest Status: *{$statusLabel}*\n\n"
            . "{$statusText}\n\n"
            . "Track your order progress here:\n"
            . "{$trackingUrl}\n\n"
            . "Thank you for trusting Bewole Jepara Furniture🙏.";
    }

    /**
     * Check connection status with the WhatsApp microservice.
     */
    public function checkConnectionStatus(): array
    {
        if (empty($this->apiUrl)) {
            return [
                'connected' => false,
                'status_text' => 'Belum Dikonfigurasi',
                'message' => 'WHATSAPP_API_URL belum diatur di file .env.',
                'html' => null,
                'qr_url' => null,
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(8)
                ->get("{$this->apiUrl}/qr", [
                    'key' => $this->apiKey,
                ]);

            if (!$response->successful()) {
                return [
                    'connected' => false,
                    'status_text' => 'Gagal Terhubung (HTTP ' . $response->status() . ')',
                    'message' => 'Server WhatsApp merespon dengan status ' . $response->status(),
                    'html' => null,
                    'qr_url' => "{$this->apiUrl}/qr?key={$this->apiKey}",
                ];
            }

            $body = $response->body();
            $isConnected = str_contains(strtolower($body), 'sudah terhubung') || str_contains(strtolower($body), 'connected');

            return [
                'connected' => $isConnected,
                'status_text' => $isConnected ? 'Terhubung (Online)' : 'Perlu Scan QR (Offline)',
                'message' => $isConnected 
                    ? 'WhatsApp Gateway aktif dan siap mengirim notifikasi.' 
                    : 'Sesi WhatsApp terputus. Silakan scan QR code untuk menghubungkan kembali.',
                'html' => $body,
                'qr_url' => "{$this->apiUrl}/qr?key={$this->apiKey}",
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'status_text' => 'Server Offline / Timeout',
                'message' => 'Gagal menghubungi server WhatsApp: ' . $e->getMessage(),
                'html' => null,
                'qr_url' => "{$this->apiUrl}/qr?key={$this->apiKey}",
            ];
        }
    }

    /**
     * Send a test message to verify the bot.
     */
    public function sendTestMessage(string $phone, string $message): array
    {
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        if (!$normalizedPhone) {
            return [
                'success' => false,
                'message' => 'Format nomor WhatsApp tidak valid. Pastikan nomor benar.',
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(12)
                ->post("{$this->apiUrl}/api/send-message", [
                    'phone' => $normalizedPhone,
                    'message' => $message,
                ]);

            $isSuccess = $response->successful() && ($response->json('success') === true);

            return [
                'success' => $isSuccess,
                'message' => $isSuccess ? 'Pesan uji coba berhasil terkirim ke ' . $normalizedPhone : ($response->json('message') ?? 'Gagal mengirim pesan dari gateway.'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Resend an existing notification log.
     */
    public function resendNotification(WhatsAppNotificationLog $log): bool
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(12)
                ->post("{$this->apiUrl}/api/send-message", [
                    'phone' => $log->recipient_phone,
                    'message' => $log->message,
                ]);

            $isSuccess = $response->successful() && ($response->json('success') === true);

            $log->update([
                'status' => $isSuccess ? 'sent' : 'failed',
                'error_message' => $isSuccess ? null : ($response->body() ?: 'Gagal saat kirim ulang'),
            ]);

            return $isSuccess;
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
