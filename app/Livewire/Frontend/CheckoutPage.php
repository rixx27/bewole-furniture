<?php

namespace App\Livewire\Frontend;

use App\Helpers\WebsiteSettings;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderCodeGenerator;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CheckoutPage extends Component
{
    public array $cart = [];
    public int $subtotal = 0;

    public string $customer_name = '';
    public string $customer_phone = '';
    public string $customer_email = '';
    public string $shipping_address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $notes = '';

    protected array $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:30',
        'customer_email' => 'nullable|email|max:255',
        'shipping_address' => 'required|string',
        'city' => 'required|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount(): void
    {
        if (!Auth::check()) {
            redirect()->route('login');
            return;
        }

        $cartService = app(CartService::class);
        $this->cart = $cartService->getCart();
        $this->subtotal = $cartService->getSubtotal();

        if (empty($this->cart)) {
            redirect()->route('products.index');
            return;
        }

        $user = Auth::user();
        $this->customer_name = $user->name ?? '';
        $this->customer_phone = $user->phone ?? '';
        $this->customer_email = $user->email ?? '';
    }

    public function placeOrder()
    {
        $this->validate();

        $cartService = app(CartService::class);
        $currentCart = $cartService->getCart();

        if (empty($currentCart)) {
            $this->dispatch('notify', message: 'Keranjang Anda kosong.');
            return redirect()->route('products.index');
        }

        // Calculate total server-side & validate products
        $serverTotal = 0;
        $totalQuantity = 0;
        $itemSummaries = [];
        $firstProductId = null;

        foreach ($currentCart as $productId => $item) {
            $product = Product::active()->find($productId);
            if (!$product) {
                continue;
            }

            if (!$firstProductId) {
                $firstProductId = $product->id;
            }

            $qty = (int) $item['quantity'];
            $price = (int) ($product->discount_price ?? $product->price);
            $itemTotal = $price * $qty;

            $serverTotal += $itemTotal;
            $totalQuantity += $qty;

            $itemSummaries[] = "{$product->name} × {$qty} (Rp " . number_format($itemTotal, 0, ',', '.') . ")";
        }

        if ($serverTotal <= 0 || !$firstProductId) {
            $this->dispatch('notify', message: 'Produk dalam keranjang Anda tidak valid atau tidak tersedia.');
            return redirect()->route('products.index');
        }

        // Format notes with item breakdown if multiple items exist
        $itemsText = implode("\n", $itemSummaries);
        $fullNotes = "Item Pesanan:\n" . $itemsText;
        if (!empty($this->notes)) {
            $fullNotes .= "\n\nCatatan Tambahan:\n" . $this->notes;
        }

        // Create order via OrderService
        $orderService = app(OrderService::class);
        $orderData = [
            'user_id' => Auth::id(),
            'product_id' => $firstProductId,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'shipping_address' => $this->shipping_address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'quantity' => $totalQuantity,
            'total_price' => $serverTotal,
            'notes' => $fullNotes,
            'whatsapp_number' => $this->customer_phone,
        ];

        $order = $orderService->createOrder($orderData);

        // Clear cart
        $cartService->clear();

        // Build WhatsApp Redirect Message
        $adminWhatsapp = WebsiteSettings::get('whatsapp') ?: WebsiteSettings::get('phone') ?: '6281234567890';
        $adminWhatsapp = preg_replace('/[^0-9]/', '', $adminWhatsapp);
        if (str_starts_with($adminWhatsapp, '0')) {
            $adminWhatsapp = '62' . substr($adminWhatsapp, 1);
        }

        $waMessage = "Halo Bewole Furniture, saya ingin konfirmasi pesanan baru.\n\n"
            . "*Kode Pesanan:* {$order->order_code}\n"
            . "*Nama:* {$this->customer_name}\n"
            . "*No. WhatsApp:* {$this->customer_phone}\n\n"
            . "*Detail Pesanan:*\n" . $itemsText . "\n\n"
            . "*Total Pembayaran:* Rp " . number_format($serverTotal, 0, ',', '.') . "\n\n"
            . "*Alamat Pengiriman:*\n{$this->shipping_address}, {$this->city} {$this->postal_code}\n";

        if (!empty($this->notes)) {
            $waMessage .= "\n*Catatan:* {$this->notes}\n";
        }

        $waMessage .= "\nMohon diproses lebih lanjut. Terima kasih!";

        $waUrl = "https://wa.me/{$adminWhatsapp}?text=" . rawurlencode($waMessage);

        return redirect()->away($waUrl);
    }

    public function render()
    {
        return view('livewire.frontend.checkout-page');
    }
}
