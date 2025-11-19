<?php declare(strict_types=1); 

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@shopme.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+33 1 23 45 67 89',
            'address' => '123 Rue de Paris, 75001 Paris',
        ]);

        $client = User::create([
            'name' => 'Jean Dupont',
            'email' => 'client@shopme.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '+33 6 12 34 56 78',
            'address' => '45 Avenue des Champs, 75008 Paris',
        ]);

        // Créer des catégories parentes
        $electronics = Category::create([
            'name' => 'Électronique',
            'slug' => Str::slug('Électronique'),
            'description' => 'Tous les produits électroniques',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $clothing = Category::create([
            'name' => 'Vêtements',
            'slug' => Str::slug('Vêtements'),
            'description' => 'Mode et vêtements',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $home = Category::create([
            'name' => 'Maison',
            'slug' => Str::slug('Maison'),
            'description' => 'Décoration et ameublement',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Créer des sous-catégories
        $smartphones = Category::create([
            'name' => 'Smartphones',
            'slug' => Str::slug('Smartphones'),
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $laptops = Category::create([
            'name' => 'Ordinateurs portables',
            'slug' => Str::slug('Ordinateurs portables'),
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $men = Category::create([
            'name' => 'Homme',
            'slug' => Str::slug('Homme'),
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $women = Category::create([
            'name' => 'Femme',
            'slug' => Str::slug('Femme'),
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Créer des produits (prix en FCFA)
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'slug' => Str::slug('iPhone 15 Pro'),
                'description' => 'Le dernier smartphone Apple avec écran ProMotion et puce A17 Pro.',
                'short_description' => 'Smartphone haut de gamme avec écran 6.1 pouces',
                'category_id' => $smartphones->id,
                'price' => 785000,
                'sale_price' => 720000,
                'is_on_sale' => true,
                'stock_quantity' => 50,
                'in_stock' => true,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'slug' => Str::slug('Samsung Galaxy S24'),
                'description' => 'Smartphone Android avec écran AMOLED et appareil photo 108MP.',
                'short_description' => 'Smartphone Android premium',
                'category_id' => $smartphones->id,
                'price' => 589000,
                'stock_quantity' => 30,
                'in_stock' => true,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'MacBook Pro 14"',
                'slug' => Str::slug('MacBook Pro 14'),
                'description' => 'Ordinateur portable Apple avec puce M3 Pro et écran Liquid Retina XDR.',
                'short_description' => 'Ordinateur portable professionnel',
                'category_id' => $laptops->id,
                'price' => 1440000,
                'sale_price' => 1310000,
                'is_on_sale' => true,
                'stock_quantity' => 20,
                'in_stock' => true,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'T-shirt Homme Premium',
                'slug' => Str::slug('T-shirt Homme Premium'),
                'description' => 'T-shirt en coton bio, confortable et durable.',
                'short_description' => 'T-shirt en coton bio',
                'category_id' => $men->id,
                'price' => 19650,
                'stock_quantity' => 100,
                'in_stock' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Robe Femme Élégante',
                'slug' => Str::slug('Robe Femme Élégante'),
                'description' => 'Robe élégante pour toutes occasions, confectionnée en tissu de qualité.',
                'short_description' => 'Robe élégante polyvalente',
                'category_id' => $women->id,
                'price' => 52400,
                'sale_price' => 39300,
                'is_on_sale' => true,
                'stock_quantity' => 60,
                'in_stock' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Canapé Moderne',
                'slug' => Str::slug('Canapé Moderne'),
                'description' => 'Canapé confortable avec revêtement en tissu résistant.',
                'short_description' => 'Canapé 3 places confortable',
                'category_id' => $home->id,
                'price' => 589000,
                'stock_quantity' => 10,
                'in_stock' => true,
                'status' => 'active',
            ],
        ];

        foreach ($products as $productData) {
            Product::create(array_merge($productData, [
                'sku' => 'SKU-' . Str::random(8),
            ]));
        }
    }
}
