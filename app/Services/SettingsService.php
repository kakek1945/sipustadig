<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Default settings for the application.
     */
    protected array $defaults = [
        // Loan settings
        'loan.max_borrow_days' => ['value' => 7, 'type' => 'number', 'description' => 'Maksimal hari pinjam'],
        'loan.max_books' => ['value' => 3, 'type' => 'number', 'description' => 'Maksimal buku per pinjam'],
        'loan.max_extensions' => ['value' => 2, 'type' => 'number', 'description' => 'Maksimal perpanjangan'],
        'loan.max_overdue_days' => ['value' => 14, 'type' => 'number', 'description' => 'Maksimal hari terlambat'],

        // Fine settings
        'fine.daily_rate' => ['value' => 1000, 'type' => 'number', 'description' => 'Denda per hari terlambat'],
        'fine.max_amount' => ['value' => 50000, 'type' => 'number', 'description' => 'Maksimal denda per pinjam'],
        'fine.waiver_requires_reason' => ['value' => true, 'type' => 'boolean', 'description' => 'Hapusan denda wajib alasan'],

        // Member settings
        'member.max_unpaid_fines' => ['value' => 50000, 'type' => 'number', 'description' => 'Maksimal denda belum lunas'],
        'member.card_validity_years' => ['value' => 3, 'type' => 'number', 'description' => 'Masa berlaku kartu (tahun)'],

        // Library info
        'library.name' => ['value' => 'Perpustakaan Sekolah', 'type' => 'string', 'description' => 'Nama Perpustakaan'],
        'library.school_name' => ['value' => 'Sekolah', 'type' => 'string', 'description' => 'Nama Sekolah'],
        'library.npsn' => ['value' => '', 'type' => 'string', 'description' => 'NSP/NPSN Sekolah'],
        'library.address' => ['value' => '', 'type' => 'string', 'description' => 'Alamat Perpustakaan'],
        'library.phone' => ['value' => '', 'type' => 'string', 'description' => 'Telepon Perpustakaan'],
        'library.email' => ['value' => '', 'type' => 'string', 'description' => 'Email Perpustakaan'],
        'library.service_hours' => ['value' => '', 'type' => 'string', 'description' => 'Jam Layanan Perpustakaan'],
        'library.vision' => ['value' => '', 'type' => 'string', 'description' => 'Visi Perpustakaan'],
        'library.mission' => ['value' => '', 'type' => 'string', 'description' => 'Misi Perpustakaan'],
        'library.goals' => ['value' => '', 'type' => 'string', 'description' => 'Tujuan Perpustakaan'],
        'library.sop_summary' => ['value' => '', 'type' => 'string', 'description' => 'Ringkasan SOP Perpustakaan'],
        'library.sop_flow' => ['value' => '', 'type' => 'string', 'description' => 'Alur Layanan Perpustakaan'],
        'library.sop_document_url' => ['value' => '', 'type' => 'string', 'description' => 'Link Dokumen SOP'],
        'library.principal_name' => ['value' => '', 'type' => 'string', 'description' => 'Nama Kepala Sekolah'],
        'library.principal_title' => ['value' => 'Kepala Sekolah', 'type' => 'string', 'description' => 'Jabatan Kepala Sekolah'],
        'library.principal_speech' => ['value' => '', 'type' => 'string', 'description' => 'Sambutan Kepala Sekolah'],
        'library.principal_photo' => ['value' => '', 'type' => 'string', 'description' => 'Foto Kepala Sekolah'],

        // System settings
        'system.auto_assign_barcode' => ['value' => true, 'type' => 'boolean', 'description' => 'Auto generate barcode'],
        'system.auto_assign_qrcode' => ['value' => true, 'type' => 'boolean', 'description' => 'Auto generate QR code'],
    ];

    /**
     * Get a setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->getSetting($key);

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'number' => (float) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Get setting model.
     */
    public function getSetting(string $key): ?Setting
    {
        $cacheKey = "setting.{$key}";

        if (Cache::has($cacheKey)) {
            $setting = Cache::get($cacheKey);

            if ($setting instanceof Setting || $setting === null) {
                return $setting;
            }

            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3600, function () use ($key) {
            return Setting::where('key', $key)->first();
        });
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, mixed $value): void
    {
        $setting = $this->getSetting($key);
        $type = $setting?->type ?? $this->getDefaultType($key);

        $stringValue = match ($type) {
            'number' => (string) $value,
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
            ]
        );

        Cache::forget("setting.{$key}");
        Cache::forget('settings.all');
    }

    /**
     * Get default type for a key.
     */
    public function getDefaultType(string $key): string
    {
        return $this->defaults[$key]['type'] ?? 'string';
    }

    public function defaults(): array
    {
        return $this->defaults;
    }

    /**
     * Get all settings.
     */
    public function all(): array
    {
        $cacheKey = 'settings.all';

        if (Cache::has($cacheKey)) {
            $settings = Cache::get($cacheKey);

            if (is_array($settings)) {
                return $settings;
            }

            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3600, function () {
            $settings = Setting::all()->keyBy('key')->all();

            foreach ($this->defaults as $key => $config) {
                if (!isset($settings[$key])) {
                    $settings[$key] = (object) [
                        'key' => $key,
                        'value' => $config['value'],
                        'type' => $config['type'],
                    ];
                }
            }

            return $settings;
        });
    }

    /**
     * Initialize default settings.
     */
    public function initDefaults(): void
    {
        foreach ($this->defaults as $key => $config) {
            Setting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => $config['value'],
                    'type' => $config['type'],
                    'description' => $config['description'],
                ]
            );
        }

        Cache::flush();
    }

    /**
     * Get loan settings.
     */
    public function loan(): array
    {
        return [
            'max_borrow_days' => $this->get('loan.max_borrow_days', 7),
            'max_books' => $this->get('loan.max_books', 3),
            'max_extensions' => $this->get('loan.max_extensions', 2),
            'max_overdue_days' => $this->get('loan.max_overdue_days', 14),
        ];
    }

    /**
     * Get fine settings.
     */
    public function fine(): array
    {
        return [
            'daily_rate' => $this->get('fine.daily_rate', 1000),
            'max_amount' => $this->get('fine.max_amount', 50000),
            'waiver_requires_reason' => $this->get('fine.waiver_requires_reason', true),
        ];
    }
}
