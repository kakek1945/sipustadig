<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = app(SettingsService::class);
        $settings->initDefaults();

        $this->command->info('Default settings initialized.');
    }
}