<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Récupérer toutes les catégories (parentes et sous-catégories)
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Aucune catégorie trouvée. Veuillez exécuter CategorySeeder d\'abord.');
            return;
        }

        // Créer 100 produits
        $products = [];
        for ($i = 0; $i < 100; $i++) {
            // Sélectionner une catégorie aléatoire
            $category = $categories->random();
            
            // Générer un nom de produit
            $productName = $faker->words(rand(2, 4), true);
            $productName = ucwords($productName);
            
            // Générer un prix en FCFA (entre 5000 et 2000000)
            $price = rand(5000, 2000000);
            $price = round($price / 100) * 100; // Arrondir à la centaine
            
            // 30% de chance d'être en promotion
            $isOnSale = rand(1, 100) <= 30;
            $salePrice = null;
            if ($isOnSale) {
                $discount = rand(10, 40); // Réduction de 10% à 40%
                $salePrice = round($price * (1 - $discount / 100) / 100) * 100;
            }
            
            // 20% de chance d'être en vedette
            $featured = rand(1, 100) <= 20;
            
            // Statut aléatoire (90% actifs)
            $statuses = ['active', 'active', 'active', 'active', 'active', 'active', 'active', 'active', 'active', 'inactive'];
            $status = $statuses[array_rand($statuses)];
            
            // Stock aléatoire
            $stockQuantity = rand(0, 200);
            $inStock = $stockQuantity > 0;
            
            $products[] = [
                'name' => $productName,
                'slug' => Str::slug($productName . ' ' . $i),
                'description' => $faker->paragraphs(rand(2, 4), true),
                'short_description' => $faker->sentence(rand(8, 15)),
                'category_id' => $category->id, // Catégorie principale
                'price' => $price,
                'sale_price' => $salePrice,
                'is_on_sale' => $isOnSale,
                'stock_quantity' => $stockQuantity,
                'in_stock' => $inStock,
                'status' => $status,
                'featured' => $featured,
                'weight' => rand(100, 5000) / 100, // Entre 1g et 50kg
                'views' => rand(0, 1000),
                'sales_count' => rand(0, 500),
            ];
        }

        foreach ($products as $productData) {
            // Extraire category_id du tableau
            $categoryId = $productData['category_id'];
            unset($productData['category_id']);
            
            // Créer ou récupérer le produit
            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, [
                    'sku' => 'SKU-' . strtoupper(Str::random(10)),
                    'category_id' => $categoryId, // Catégorie principale pour compatibilité
                ])
            );
            
            // Associer le produit à la catégorie via la relation many-to-many
            $category = Category::with('parent')->find($categoryId);
            if ($category) {
                // Recharger les relations du produit
                $product->refresh();
                $product->load('categories');
                
                // Associer à la catégorie principale si pas déjà associé
                if (!$product->categories->contains($categoryId)) {
                    $product->categories()->attach($categoryId);
                }
                
                // Si la catégorie a un parent, associer aussi le produit à la catégorie parente
                if ($category->parent_id) {
                    $product->load('categories');
                    if (!$product->categories->contains($category->parent_id)) {
                        $product->categories()->attach($category->parent_id);
                    }
                }
                
                // 30% de chance d'associer le produit à une autre catégorie supplémentaire
                if (rand(1, 100) <= 30) {
                    $additionalCategory = $categories->where('id', '!=', $categoryId)->random();
                    $product->load('categories');
                    if (!$product->categories->contains($additionalCategory->id)) {
                        $product->categories()->attach($additionalCategory->id);
                    }
                }
            }
        }

        $this->command->info('100 produits créés avec succès.');
    }
}
