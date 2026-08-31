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

    // Meubel & Packing selections
    public string $meubel_type = ''; // 'mentah' or 'matang'
    public string $packing_type = ''; // 'kardus' or 'plastik'
    public array $customization_selections = []; // [product_id => selection]
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
        'meubel_type' => 'required|in:mentah,matang',
        'packing_type' => 'required|in:kardus,plastik',
    ];

    protected array $messages = [
        'customer_name.required' => 'Nama Lengkap wajib diisi.',
        'customer_phone.required' => 'Nomor WhatsApp wajib diisi.',
        'province.required' => 'Provinsi wajib dipilih.',
        'city.required' => 'Kota / Kabupaten wajib dipilih.',
        'shipping_address.required' => 'Alamat Lengkap wajib diisi.',
        'meubel_type.required' => 'Jenis Meubel wajib dipilih.',
        'meubel_type.in' => 'Pilihan Jenis Meubel tidak valid.',
        'packing_type.required' => 'Bahan Packing wajib dipilih.',
        'packing_type.in' => 'Pilihan Bahan Packing tidak valid.',
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
        if ($value === 'mentah') {
            $this->customization_selections = [];
            $this->resetErrorBag('customization_selections');
        }
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

    public function placeOrder()
    {
        $this->validate();

        // Custom validation for Meubel Matang customization per product
        if ($this->meubel_type === 'mentah') {
            $this->customization_selections = [];
        } elseif ($this->meubel_type === 'matang') {
            $hasError = false;
            foreach ($this->cart as $productId => $item) {
                $product = Product::active()->find($productId);
                if (!$product) {
                    continue;
                }

                $options = $product->getCustomizationOptions();
                if ($options && !empty($options['required'])) {
                    $selection = $this->customization_selections[$productId] ?? null;
                    if (empty($selection)) {
                        $this->addError("customization_selections.{$productId}", "Pilihan {$options['label']} untuk {$product->name} wajib dipilih.");
                        $hasError = true;
                    }
                }
            }

            if ($hasError) {
                return;
            }
        }

        $cartService = app(CartService::class);
        $currentCart = $cartService->getCart();

        if (empty($currentCart)) {
            $this->dispatch('notify', message: 'Keranjang Anda kosong.');
            return redirect()->route('products.index');
        }

        // Calculate total server-side & validate products
        $serverSubtotal = 0;
        $totalQuantity = 0;
        $itemSummaries = [];
        $firstProductId = null;
        $customizationTexts = [];

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

            $serverSubtotal += $itemTotal;
            $totalQuantity += $qty;

            $itemSummaries[] = "{$product->name} × {$qty} (Rp " . number_format($itemTotal, 0, ',', '.') . ")";

            if ($this->meubel_type === 'matang' && isset($this->customization_selections[$productId])) {
                $custVal = $this->customization_selections[$productId];
                $customizationTexts[] = "Bahan Dudukan ({$product->name}): {$custVal}";
            }
        }

        if ($serverSubtotal <= 0 || !$firstProductId) {
            $this->dispatch('notify', message: 'Produk dalam keranjang Anda tidak valid atau tidak tersedia.');
            return redirect()->route('products.index');
        }

        $serverCustomFee = 0; // Default 0 if not configured
        $serverPackingFee = 0; // Default 0 if not configured
        $serverTotal = $serverSubtotal + $serverCustomFee + $serverPackingFee;

        $meubelText = $this->meubel_type === 'matang' ? 'Meubel Matang' : 'Meubel Mentah';
        $packingText = $this->packing_type === 'kardus' ? 'Kardus' : 'Plastik';

        // Format full notes for internal log
        $itemsText = implode("\n", $itemSummaries);
        $fullNotes = "Detail Customisasi & Packing:\n"
            . "Jenis Meubel: {$meubelText}\n";
        if ($this->meubel_type === 'matang' && !empty($customizationTexts)) {
            $fullNotes .= implode("\n", $customizationTexts) . "\n";
        }
        $fullNotes .= "Bahan Packing: {$packingText}\n\n"
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
            'packing_type' => $this->packing_type,
            'customization_details' => $this->meubel_type === 'matang' ? $this->customization_selections : null,
            'customization_fee' => $serverCustomFee,
            'packing_fee' => $serverPackingFee,
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

        if ($this->meubel_type === 'matang' && !empty($customizationTexts)) {
            foreach ($customizationTexts as $cText) {
                $waMessage .= "{$cText}\n";
            }
            $waMessage .= "\n";
        }

        $waMessage .= "Bahan Packing:\n{$packingText}\n\n";
        $waMessage .= "Alamat Pengiriman:\n{$this->shipping_address}, {$this->city}, Provinsi {$this->province}\n\n";

        if (!empty($this->notes)) {
            $waMessage .= "Catatan:\n{$this->notes}\n\n";
        }

        $waMessage .= "Subtotal:\nRp " . number_format($serverSubtotal, 0, ',', '.') . "\n\n";
        $waMessage .= "Biaya Customisasi:\nRp " . number_format($serverCustomFee, 0, ',', '.') . "\n\n";
        $waMessage .= "Biaya Packing:\nRp " . number_format($serverPackingFee, 0, ',', '.') . "\n\n";
        $waMessage .= "Total:\nRp " . number_format($serverTotal, 0, ',', '.');

        $waUrl = "https://wa.me/{$adminWhatsapp}?text=" . rawurlencode($waMessage);

        return redirect()->away($waUrl);
    }

    public function render()
    {
        return view('livewire.frontend.checkout-page');
    }
}
