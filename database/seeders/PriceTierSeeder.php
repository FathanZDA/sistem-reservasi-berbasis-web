<?php

namespace Database\Seeders;

use App\Models\PriceTier;
use Illuminate\Database\Seeder;

class PriceTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['label' => '1–5 orang', 'min_orang' => 1, 'max_orang' => 5, 'tarif_per_orang' => 50000],
            ['label' => '6–10 orang', 'min_orang' => 6, 'max_orang' => 10, 'tarif_per_orang' => 45000],
            ['label' => '11 orang atau lebih', 'min_orang' => 11, 'max_orang' => null, 'tarif_per_orang' => 40000],
        ];

        foreach ($tiers as $tier) {
            PriceTier::updateOrCreate(
                ['min_orang' => $tier['min_orang']],
                $tier + ['is_active' => true]
            );
        }
    }
}
