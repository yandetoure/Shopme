<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'BIENVENUE10',
                'name' => 'Remise de bienvenue',
                'description' => 'Remise de 10% sur votre première commande',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 10000, // 10 000 FCFA minimum
                'maximum_discount' => 50000, // Max 50 000 FCFA
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'PROMO5000',
                'name' => 'Remise fixe de 5 000 FCFA',
                'description' => 'Réduction de 5 000 FCFA sur votre commande',
                'type' => 'fixed',
                'value' => 5000,
                'minimum_amount' => 20000, // 20 000 FCFA minimum
                'maximum_discount' => null,
                'usage_limit' => 50,
                'usage_limit_per_user' => 2,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday - 25%',
                'description' => 'Remise exceptionnelle de 25% pour Black Friday',
                'type' => 'percentage',
                'value' => 25,
                'minimum_amount' => 50000, // 50 000 FCFA minimum
                'maximum_discount' => 100000, // Max 100 000 FCFA
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(7),
                'is_active' => true,
            ],
            [
                'code' => 'NOEL20',
                'name' => 'Promotion Noël',
                'description' => 'Remise de 20% pour les fêtes',
                'type' => 'percentage',
                'value' => 20,
                'minimum_amount' => 30000, // 30 000 FCFA minimum
                'maximum_discount' => 75000, // Max 75 000 FCFA
                'usage_limit' => 150,
                'usage_limit_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(1),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(
                ['code' => strtoupper($coupon['code'])],
                $coupon
            );
        }

        $this->command->info(count($coupons) . ' codes promo créés avec succès.');
    }
}
