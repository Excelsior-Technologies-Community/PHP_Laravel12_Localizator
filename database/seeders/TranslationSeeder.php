<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;  // <-- add this line

class TranslationSeeder extends Seeder
{
    public function run()
    {
        $keys = [
            'welcome',
            'login',
            'logout',
            'dashboard',
        ];

        foreach ($keys as $key) {
            Translation::create(['key' => $key, 'value' => []]);
        }
    }
}