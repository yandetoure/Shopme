<?php declare(strict_types=1); 

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,  // Doit être exécuté en premier
            CategorySeeder::class,         // Les catégories doivent exister avant les produits
            UserSeeder::class,             // Les utilisateurs peuvent être créés après les rôles
            ProductSeeder::class,          // Les produits nécessitent les catégories
            ShippingRateSeeder::class,     // Tarifs de livraison
            CouponSeeder::class,           // Codes promo
        ]);
    }
}
