<?php

namespace App\Livewire\Admin\Whatsapp;

use App\Helpers\WebsiteSettings;
use App\Models\WebsiteSetting;
use App\Models\WhatsAppNotificationLog;
use App\Services\WebsiteSettingService;
use App\Services\WhatsAppNotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class WhatsappManager extends Component
{
    use WithPagination;

    public bool $botEnabled = true;
    public array $connectionStatus = [];
    public string $testPhone = '';
    public string $testMessage = 'Halo! Ini adalah pesan uji coba dari sistem bot WhatsApp Bewole Jepara Furniture.';
    public ?string $testResult = null;
    public bool $testSuccess = false;
    public string $search = '';
    public string $statusFilter = '';

    public function mount(WhatsAppNotificationService $waService): void
    {
        $this->botEnabled = (bool) WebsiteSettings::get('whatsapp_notification_enabled', true);
        $this->checkConnection($waService);
    }

    public function toggleBot(): void
    {
        $this->botEnabled = !$this->botEnabled;

        $setting = WebsiteSetting::first();
        if ($setting) {
            $setting->update([
                'whatsapp_notification_enabled' => $this->botEnabled,
            ]);
        }

        // Invalidate settings cache if service uses caching
        cache()->forget('website_settings_frontend');

        $statusText = $this->botEnabled ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('notify', 
            type: $this->botEnabled ? 'success' : 'warning', 
            message: "Bot WhatsApp berhasil {$statusText}."
        );
    }

    public function checkConnection(WhatsAppNotificationService $waService): void
    {
        $this->connectionStatus = $waService->checkConnectionStatus();
    }

    public function sendTest(WhatsAppNotificationService $waService): void
    {
        $this->validate([
            'testPhone' => 'required|string|min:8|max:20',
            'testMessage' => 'required|string|max:500',
        ], [
            'testPhone.required' => 'Nomor WhatsApp wajib diisi.',
            'testMessage.required' => 'Pesan uji coba wajib diisi.',
        ]);

        $res = $waService->sendTestMessage($this->testPhone, $this->testMessage);

        $this->testSuccess = $res['success'];
        $this->testResult = $res['message'];

        $this->dispatch('notify', 
            type: $this->testSuccess ? 'success' : 'error', 
            message: $this->testResult
        );
    }

    public function resendLog(int $logId, WhatsAppNotificationService $waService): void
    {
        $log = WhatsAppNotificationLog::find($logId);
        if (!$log) {
            $this->dispatch('notify', type: 'error', message: 'Log notifikasi tidak ditemukan.');
            return;
        }

        $success = $waService->resendNotification($log);

        if ($success) {
            $this->dispatch('notify', type: 'success', message: "Notifikasi berhasil dikirim ulang ke {$log->recipient_phone}.");
        } else {
            $this->dispatch('notify', type: 'error', message: "Gagal mengirim ulang: " . ($log->error_message ?: 'Error gateway'));
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WhatsAppNotificationLog::with('order')->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_code', 'like', '%' . $this->search . '%')
                  ->orWhere('recipient_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $logs = $query->paginate(15);

        return view('livewire.admin.whatsapp.whatsapp-manager', [
            'logs' => $logs,
        ])->layout('layouts.admin', ['title' => 'Integrasi WhatsApp Bot']);
    }
}
