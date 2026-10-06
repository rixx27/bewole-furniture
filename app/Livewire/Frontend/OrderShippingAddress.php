<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderShippingAddress extends Component
{
    public int $orderId;
    public ?Order $order = null;

    public bool $showModal = false;
    public string $shipping_address = '';
    public string $province = '';
    public string $city = '';
    public string $postal_code = '';

    public array $provinces = [];
    public array $cities = [];

    protected function rules(): array
    {
        return [
            'province' => 'required|string',
            'city' => 'required|string|max:100',
            'shipping_address' => 'required|string|min:10|max:1000',
            'postal_code' => 'nullable|string|max:10',
        ];
    }

    protected $messages = [
        'province.required' => 'Provinsi wajib dipilih.',
        'city.required' => 'Kota atau kabupaten wajib dipilih.',
        'shipping_address.required' => 'Alamat lengkap wajib diisi.',
        'shipping_address.min' => 'Alamat lengkap minimal 10 karakter (cantumkan jalan, RT/RW, kelurahan/kecamatan).',
        'shipping_address.max' => 'Alamat lengkap maksimal 1000 karakter.',
    ];

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
        $this->loadProvinces();
        $this->loadOrder();
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

    public function loadOrder(): void
    {
        $this->order = Order::find($this->orderId);

        if ($this->order) {
            $rawAddr = $this->order->shipping_address ?? '';

            // Extract province if already saved in (Provinsi: ...)
            $extractedProvince = '';
            if (preg_match('/\(Provinsi:\s*([^)]+)\)/i', $rawAddr, $matches)) {
                $extractedProvince = trim($matches[1]);
            }

            // Clean address string by removing any (Provinsi: ...) tag
            $cleanAddr = trim(preg_replace('/\s*\(Provinsi:.*?\)\s*/i', '', $rawAddr));
            $this->shipping_address = ($cleanAddr && $cleanAddr !== '-') ? $cleanAddr : '';

            $cityVal = $this->order->city;
            $this->city = ($cityVal && $cityVal !== '-') ? $cityVal : '';
            $this->postal_code = $this->order->postal_code ?? '';

            // Load matching province and city list
            $path = storage_path('app/indonesia_regions.json');
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);

                if ($extractedProvince && isset($data[$extractedProvince])) {
                    $this->province = $extractedProvince;
                    $this->cities = $data[$extractedProvince];
                } elseif (!empty($this->city)) {
                    // Search province by city name
                    foreach ($data as $provName => $cityList) {
                        if (in_array($this->city, $cityList)) {
                            $this->province = $provName;
                            $this->cities = $cityList;
                            break;
                        }
                    }
                }
            }
        }
    }

    #[On('openAddressModal')]
    public function openModal(): void
    {
        $this->loadProvinces();
        $this->loadOrder();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function saveAddress(): void
    {
        if (!$this->order) {
            return;
        }

        // Pastikan hanya pemilik pesanan atau admin yang dapat mengubah alamat
        if ($this->order->user_id && $this->order->user_id !== Auth::id() && !Auth::user()?->hasRole('admin')) {
            abort(403, 'Anda tidak berhak mengubah alamat pesanan ini.');
        }

        $this->validate();

        $cleanAddress = trim(preg_replace('/\s*\(Provinsi:.*?\)\s*/i', '', $this->shipping_address));
        $fullAddress = $cleanAddress;
        if (!empty($this->province)) {
            $fullAddress .= ' (Provinsi: ' . trim($this->province) . ')';
        }

        $this->order->shipping_address = $fullAddress;
        $this->order->city = trim($this->city);
        $this->order->postal_code = trim($this->postal_code) ?: null;
        $this->order->save();

        // Catat riwayat perubahan alamat
        $this->order->statusHistories()->create([
            'status' => $this->order->status,
            'description' => 'Pelanggan telah mengisi/memperbarui alamat pengiriman: ' . $fullAddress . ', ' . $this->order->city . ($this->order->postal_code ? ' (' . $this->order->postal_code . ')' : '') . '.',
            'changed_by' => Auth::id(),
        ]);

        $this->loadOrder();
        $this->showModal = false;

        $this->dispatch('addressUpdated');
        $this->dispatch('notify', type: 'success', message: 'Alamat pengiriman berhasil disimpan!');
    }

    public function render()
    {
        $isAddressEmpty = empty($this->order?->shipping_address) || $this->order?->shipping_address === '-';

        return view('livewire.frontend.order-shipping-address', [
            'isAddressEmpty' => $isAddressEmpty,
        ]);
    }
}
