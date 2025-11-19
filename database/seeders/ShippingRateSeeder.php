<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            [
                'name' => 'Livraison Standard',
                'slug' => 'standard',
                'description' => 'Livraison standard en 5-7 jours ouvrables',
                'price' => 3000, // 3000 FCFA
                'min_order_amount' => null,
                'max_order_amount' => 50000, // Gratuit au-delà de 50k
                'is_free' => false,
                'estimated_days' => 7,
                'sort_order' => 1,
            ],
            [
                'name' => 'Livraison Express',
                'slug' => 'express',
                'description' => 'Livraison express en 2-3 jours ouvrables',
                'price' => 6550, // 6550 FCFA
                'min_order_amount' => null,
                'max_order_amount' => null,
                'is_free' => false,
                'estimated_days' => 3,
                'sort_order' => 2,
            ],
            [
                'name' => 'Livraison Gratuite',
                'slug' => 'gratuite',
                'description' => 'Livraison gratuite pour les commandes de plus de 100 000 FCFA',
                'price' => 0,
                'min_order_amount' => 100000, // Gratuit à partir de 100k
                'max_order_amount' => null,
                'is_free' => true,
                'estimated_days' => 7,
                'sort_order' => 3,
            ],
        ];

        foreach ($rates as $rate) {
            ShippingRate::firstOrCreate(
                ['slug' => $rate['slug']],
                $rate
            );
        }

        $this->command->info(count($rates) . ' tarifs de livraison créés avec succès.');
    }
}
