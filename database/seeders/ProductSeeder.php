<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les catégories
        $smartphones = Category::where('slug', 'smartphones')->first();
        $laptops = Category::where('slug', 'ordinateurs-portables')->first();
        $men = Category::where('slug', 'homme')->first();
        $women = Category::where('slug', 'femme')->first();
        $home = Category::where('slug', 'maison')->first();

        if (!$smartphones || !$laptops || !$men || !$women || !$home) {
            $this->command->warn('Certaines catégories n\'existent pas. Veuillez exécuter CategorySeeder d\'abord.');
            return;
        }

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
            // Extraire category_id du tableau
            $categoryId = $productData['category_id'];
            unset($productData['category_id']);
            
            // Créer ou récupérer le produit
            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, [
                    'sku' => 'SKU-' . Str::random(8),
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
            }
        }

        $this->command->info(count($products) . ' produits créés avec succès.');
    }
}
