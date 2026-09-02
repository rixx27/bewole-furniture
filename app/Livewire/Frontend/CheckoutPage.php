<?php

namespace App\Livewire\Frontend;

use App\Helpers\WebsiteSettings;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
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
    public string $province = '';
    public string $shipping_address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $notes = '';

    // Meubel selection
    public string $meubel_type = ''; // 'unfinished' or 'finished'
    public ?string $packing_type = null;
    public ?array $customization_selections = [];
    public int $customization_fee = 0;
    public int $packing_fee = 0;

    public array $provinces = [];
    public array $cities = [];

    protected array $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:30',
        'customer_email' => 'nullable|email|max:255',
        'province' => 'required|string',
        'shipping_address' => 'required|string',
        'city' => 'required|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'notes' => 'nullable|string|max:1000',
        'meubel_type' => 'required|in:unfinished,finished,mentah,matang',
    ];

    protected array $messages = [
        'customer_name.required' => 'Nama Lengkap wajib diisi.',
        'customer_phone.required' => 'Nomor WhatsApp wajib diisi.',
        'province.required' => 'Provinsi wajib dipilih.',
        'city.required' => 'Kota / Kabupaten wajib dipilih.',
        'shipping_address.required' => 'Alamat Lengkap wajib diisi.',
        'meubel_type.required' => 'Pilihan Meubel (Unfinished / Finished) wajib dipilih.',
        'meubel_type.in' => 'Pilihan Jenis Meubel tidak valid.',
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

        $this->loadProvinces();

        if (empty($this->cart)) {
            session()->flash('info', 'Keranjang belanja Anda masih kosong. Silakan pilih produk terlebih dahulu.');
            redirect()->route('products.index');
            return;
        }

        $user = Auth::user();
        $this->customer_name = $user->name ?? '';
        $this->customer_phone = $user->phone ?? '';
        $this->customer_email = $user->email ?? '';
    }

    public function updatedMeubelType($value): void
    {
        // Re-trigger calculation on meubel type change
    }

    public function loadProvinces(): void
    {
        $path = storage_path('app/indonesia_regions.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            $this->provinces = array_keys($data);
        }
    }

    public function updatedProvince($value): void
    {
        $this->city = '';
        $this->cities = [];

        if (!empty($value)) {
            $path = storage_path('app/indonesia_regions.json');
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if (isset($data[$value])) {
                    $this->cities = $data[$value];
                }
            }
        }
    }

    /**
     * Calculate material costs dynamically based on unit unfinished/finished prices
     */
    public function calculateMaterialCosts(): array
    {
        $totalCustomFee = 0;
        $itemBreakdowns = [];

        foreach ($this->cart as $productId => $item) {
            $product = Product::whereIn('status', ['active', 'pre_order'])->find($productId);
            if (!$product) {
                continue;
            }

            $qty = (int) ($item['quantity'] ?? 1);

            // Meubel Finished Customization Cost (Difference between price_matang/price_finished and base price)
            $seatName = null;
            $seatCost = 0;
            $seatUnitPrice = 0;

            if ($this->meubel_type === 'finished' || $this->meubel_type === 'matang') {
                $basePrice = (int) $product->price;
                $finishedPrice = (int) ($product->price_matang ?: $product->price);
                $diff = max(0, $finishedPrice - $basePrice);
                $seatUnitPrice = $diff;
                $seatCost = $diff * $qty;
                $seatName = $diff > 0 ? 'Finished' : null;
            }

            $totalCustomFee += $seatCost;

            $itemBreakdowns[$productId] = [
                'product_name' => $product->name,
                'seat_material_name' => $seatName,
                'seat_price_per_meter' => $seatUnitPrice,
                'seat_usage_meter' => 1,
                'seat_material_cost' => $seatCost,
                'packing_material_name' => null,
                'packing_price_per_meter' => 0,
                'packing_usage_meter' => 0,
                'packing_material_cost' => 0,
            ];
        }

        return [
            'customization_fee' => $totalCustomFee,
            'packing_fee' => 0,
            'item_breakdowns' => $itemBreakdowns,
        ];
    }

    public function placeOrder()
    {
        $this->validate();

        $cartService = app(CartService::class);
        $currentCart = $this->cart;

        if (empty($currentCart)) {
            $this->dispatch('notify', message: 'Keranjang Anda kosong.');
            return redirect()->route('products.index');
        }

        // Calculate total server-side & validate products
        $serverSubtotal = 0;
        $totalQuantity = 0;
        $firstProductId = null;
        $itemSummaries = [];

        foreach ($currentCart as $productId => $item) {
            $product = Product::whereIn('status', ['active', 'pre_order'])->find($productId);
            if (!$product) {
                continue;
            }

            if (!$firstProductId) {
                $firstProductId = $product->id;
            }

            $qty = (int) ($item['quantity'] ?? 1);
            $price = (int) ($product->discount_price ?? $product->price);
            $serverSubtotal += $price * $qty;
            $totalQuantity += $qty;

            $itemSummaries[] = "- {$product->name} (Qty: {$qty}) - Rp " . number_format($price * $qty, 0, ',', '.');
        }

        if ($serverSubtotal <= 0 || !$firstProductId) {
            $this->dispatch('notify', message: 'Produk dalam keranjang Anda tidak valid atau tidak tersedia.');
            return redirect()->route('products.index');
        }

        $calc = $this->calculateMaterialCosts();
        $serverCustomFee = $calc['customization_fee'];
        $serverPackingFee = 0;
        $itemBreakdowns = $calc['item_breakdowns'];
        $serverTotal = $serverSubtotal + $serverCustomFee;

        $meubelText = in_array($this->meubel_type, ['finished', 'matang']) ? 'Finished' : 'Unfinished';

        // Format full notes for internal log
        $itemsText = implode("\n", $itemSummaries);
        $fullNotes = "Detail Pilihan Meubel:\n"
            . "Jenis Meubel: {$meubelText}\n\n"
            . "Item Pesanan:\n" . $itemsText;

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
            'shipping_address' => $this->shipping_address . ' (Provinsi: ' . $this->province . ')',
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'meubel_type' => $this->meubel_type,
            'packing_type' => null,
            'customization_details' => null,
            'customization_fee' => $serverCustomFee,
            'packing_fee' => 0,
            'item_breakdowns' => $itemBreakdowns,
            'quantity' => $totalQuantity,
            'total_price' => $serverTotal,
            'notes' => $fullNotes,
            'whatsapp_number' => $this->customer_phone,
        ];

        $order = $orderService->createOrder($orderData, $currentCart);

        // Clear cart
        $cartService->clear();

        // Build WhatsApp Redirect Message according to prompt requirements
        $adminWhatsapp = WebsiteSettings::get('whatsapp') ?: WebsiteSettings::get('phone') ?: '6281234567890';
        $adminWhatsapp = preg_replace('/[^0-9]/', '', $adminWhatsapp);
        if (str_starts_with($adminWhatsapp, '0')) {
            $adminWhatsapp = '62' . substr($adminWhatsapp, 1);
        }

        $waMessage = "Halo Bewole Jepara Furniture,\n\n"
            . "Saya ingin melakukan pemesanan.\n\n"
            . "Kode Pesanan:\n{$order->order_code}\n\n"
            . "Produk:\n";

        foreach ($currentCart as $productId => $item) {
            $product = Product::find($productId);
            $pName = $product ? $product->name : ($item['name'] ?? 'Produk');
            $pQty = $item['quantity'] ?? 1;
            $waMessage .= "{$pName}\nQty: {$pQty}\n";
        }

        $waMessage .= "\nJenis Meubel:\n{$meubelText}\n\n";
        $waMessage .= "Alamat Pengiriman:\n{$this->shipping_address}, {$this->city}, Provinsi {$this->province}\n\n";

        if (!empty($this->notes)) {
            $waMessage .= "Catatan:\n{$this->notes}\n\n";
        }

        $waMessage .= "Subtotal:\nRp " . number_format($serverSubtotal, 0, ',', '.') . "\n\n";
        if ($serverCustomFee > 0) {
            $waMessage .= "Tambahan Meubel Finished:\nRp " . number_format($serverCustomFee, 0, ',', '.') . "\n\n";
        }
        $waMessage .= "Total:\nRp " . number_format($serverTotal, 0, ',', '.');

        $waUrl = "https://wa.me/{$adminWhatsapp}?text=" . rawurlencode($waMessage);

        return redirect()->away($waUrl);
    }

    public function render()
    {
        $calc = $this->calculateMaterialCosts();
        $this->customization_fee = $calc['customization_fee'];
        $this->packing_fee = 0;

        return view('livewire.frontend.checkout-page', [
            'itemBreakdowns' => $calc['item_breakdowns'],
            'totalPayable' => $this->subtotal + $this->customization_fee,
        ]);
    }
}
