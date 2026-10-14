<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiksi', 'code' => 'FIK', 'description' => 'Buku-buku fiksi termasuk novel, cerpen, dan drama', 'color' => '#6366f1', 'sort_order' => 1],
            ['name' => 'Non-Fiksi', 'code' => 'NFK', 'description' => 'Buku non-fiksi termasuk biografi, sejarah, dan sains', 'color' => '#10b981', 'sort_order' => 2],
            ['name' => 'Referensi', 'code' => 'REF', 'description' => 'Buku referensi seperti kamus, ensiklopedia', 'color' => '#f59e0b', 'sort_order' => 3],
            ['name' => 'Pendidikan', 'code' => 'PEND', 'description' => 'Buku pelajaran dan buku teks', 'color' => '#3b82f6', 'sort_order' => 4],
            ['name' => 'Agama', 'code' => 'AGM', 'description' => 'Buku-buku tentang agama dan spiritual', 'color' => '#8b5cf6', 'sort_order' => 5],
            ['name' => 'Teknologi', 'code' => 'TEK', 'description' => 'Buku tentang teknologi dan komputer', 'color' => '#ec4899', 'sort_order' => 6],
            ['name' => 'Sastra', 'code' => 'SST', 'description' => 'Karya sastra Indonesia dan dunia', 'color' => '#14b8a6', 'sort_order' => 7],
            ['name' => 'Anak-anak', 'code' => 'ANK', 'description' => 'Buku untuk anak-anak', 'color' => '#f97316', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['code' => $category['code']], $category);
        }

        $this->command->info('Default categories created.');
    }
}