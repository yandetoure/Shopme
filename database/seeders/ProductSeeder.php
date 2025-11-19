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
        $categories = Category::with('parent')->get();

        if ($categories->isEmpty()) {
            $this->command->warn('Aucune catégorie trouvée. Veuillez exécuter CategorySeeder d\'abord.');
            return;
        }

        $totalProducts = 0;

        // Pour chaque catégorie, créer 5 produits
        foreach ($categories as $category) {
            for ($i = 0; $i < 5; $i++) {
                // Générer un nom de produit en fonction de la catégorie
                $productName = $this->generateProductName($category, $faker);
                
                // Générer un prix en FCFA selon le type de catégorie
                $price = $this->generatePrice($category);
                
                // 30% de chance d'être en promotion
                $isOnSale = rand(1, 100) <= 30;
                $salePrice = null;
                if ($isOnSale) {
                    $discount = rand(10, 40); // Réduction de 10% à 40%
                    $salePrice = round($price * (1 - $discount / 100) / 100) * 100;
                }
                
                // 20% de chance d'être en vedette
                $featured = rand(1, 100) <= 20;
                
                // Statut (90% actifs)
                $statuses = ['active', 'active', 'active', 'active', 'active', 'active', 'active', 'active', 'active', 'inactive'];
                $status = $statuses[array_rand($statuses)];
                
                // Stock aléatoire
                $stockQuantity = rand(0, 200);
                $inStock = $stockQuantity > 0;
                
                // Créer le produit
                $product = Product::firstOrCreate(
                    ['slug' => Str::slug($productName . ' ' . $category->slug . ' ' . $i . ' ' . Str::random(4))],
                    [
                        'name' => $productName,
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
                        'sku' => 'SKU-' . strtoupper(Str::random(10)),
                    ]
                );
                
                // Associer le produit à la catégorie via la relation many-to-many
                $product->refresh();
                $product->load('categories');
                
                // Associer à la catégorie principale si pas déjà associé
                if (!$product->categories->contains($category->id)) {
                    $product->categories()->attach($category->id);
                }
                
                // Si la catégorie a un parent, associer aussi le produit à la catégorie parente
                if ($category->parent_id) {
                    $product->load('categories');
                    if (!$product->categories->contains($category->parent_id)) {
                        $product->categories()->attach($category->parent_id);
                    }
                }
                
                $totalProducts++;
            }
        }

        $this->command->info("{$totalProducts} produits créés avec succès (5 produits par catégorie).");
    }

    /**
     * Générer un nom de produit en fonction de la catégorie
     */
    private function generateProductName(Category $category, $faker): string
    {
        $categoryName = strtolower($category->name);
        $slug = strtolower($category->slug);
        
        // Noms de produits spécifiques selon la catégorie ou sous-catégorie
        $productNames = [
            // Chambre
            'chambre' => ['Lit', 'Armoire', 'Commode', 'Table de nuit', 'Matelas', 'Drap', 'Coussin', 'Décoration'],
            'lampes-de-chevet' => ['Lampe de chevet LED', 'Lampe de chevet moderne', 'Lampe de chevet design', 'Lampe de chevet vintage', 'Lampe de chevet colorée'],
            'parfums-de-chambre' => ['Parfum de chambre lavande', 'Parfum de chambre vanille', 'Parfum de chambre citron', 'Parfum de chambre rose', 'Parfum de chambre bois'],
            'diffuseurs' => ['Diffuseur d\'arômes électrique', 'Diffuseur d\'arômes USB', 'Diffuseur d\'arômes rechargeable', 'Diffuseur d\'arômes design', 'Diffuseur d\'arômes bambou'],
            'encensoirs' => ['Encensoir céramique', 'Encensoir métal', 'Encensoir design', 'Encensoir traditionnel', 'Encensoir suspendu'],
            
            // Cuisine
            'cuisine' => ['Machine à laver', 'Sèche-linge', 'Micro-ondes', 'Machine à café', 'Mixeur', 'Four', 'Réfrigérateur', 'Ustensile'],
            'chafing-dish' => ['Chafing dish professionnel', 'Chafing dish inox', 'Chafing dish réchaud', 'Chafing dish buffet', 'Chafing dish gastronomie'],
            
            // Toilette
            'toilette' => ['Serviette', 'Peignoir', 'Robe de chambre', 'Décoratif', 'Organisateur'],
            'ceinture-de-bain' => ['Ceinture de bain velours', 'Ceinture de bain coton', 'Ceinture de bain design', 'Ceinture de bain élégante', 'Ceinture de bain confort'],
            
            // Jardin
            'jardin' => ['Tondeuse', 'Outillage', 'Plante', 'Mobilier', 'Déco'],
            'fleurs' => ['Fleurs artificielles', 'Bouquet de fleurs', 'Fleurs séchées', 'Composition florale', 'Fleurs décoratives'],
            'pots-de-fleurs' => ['Pot de fleurs céramique', 'Pot de fleurs plastique', 'Pot de fleurs design', 'Pot de fleurs suspendu', 'Pot de fleurs décoratif'],
            
            // Maison
            'maison' => ['Meuble', 'Décoration', 'Luminaire', 'Textile', 'Accessoire'],
            'decorations-murales' => ['Tableau mural', 'Cadre photo mural', 'Décoration murale moderne', 'Panneau mural décoratif', 'Suspension murale'],
            'bougies-de-cire' => ['Bougie de cire parfumée', 'Bougie de cire naturelle', 'Bougie de cire design', 'Bougie de cire colorée', 'Bougie de cire artisanale'],
            
            // Autres
            'informatique' => ['Ordinateur', 'Accessoire', 'Périphérique', 'Composant', 'Logiciel'],
            'ordinateur' => ['PC portable', 'PC bureau', 'Moniteur', 'Clavier', 'Souris'],
            'téléphones' => ['Smartphone', 'Téléphone fixe', 'Accessoire', 'Étui', 'Coque'],
            'audio et son' => ['Enceinte', 'Amplificateur', 'Hi-Fi', 'Radio', 'Accessoire'],
            'écouteurs' => ['Écouteurs filaires', 'Écouteurs Bluetooth', 'Intra-auriculaires'],
            'casque' => ['Casque audio', 'Casque Bluetooth', 'Casque gaming'],
            'chargeurs' => ['Chargeur USB', 'Chargeur sans fil', 'Batterie externe', 'Câble'],
            'hygiène et soins' => ['Shampoing', 'Après-shampoing', 'Démêlant', 'Lait de corps', 'Savon'],
            'rangement' => ['Organisateur', 'Rangement', 'Boîte', 'Caisse', 'Étagère'],
        ];

        // Chercher d'abord par slug (plus précis pour les sous-catégories)
        foreach ($productNames as $key => $names) {
            if (str_contains($slug, $key)) {
                $prefix = $names[array_rand($names)];
                $suffix = $faker->words(rand(1, 2), true);
                return ucwords($prefix . ' ' . $suffix);
            }
        }

        // Ensuite chercher par nom de catégorie
        foreach ($productNames as $key => $names) {
            if (str_contains($categoryName, $key)) {
                $prefix = $names[array_rand($names)];
                $suffix = $faker->words(rand(1, 2), true);
                return ucwords($prefix . ' ' . $suffix);
            }
        }

        // Par défaut, générer un nom générique
        return ucwords($faker->words(rand(2, 4), true));
    }

    /**
     * Générer un prix selon le type de catégorie
     */
    private function generatePrice(Category $category): int
    {
        $categoryName = strtolower($category->name);
        $slug = strtolower($category->slug);
        
        // Prix spécifiques par slug (sous-catégories)
        if (str_contains($slug, 'diffuseur') || str_contains($slug, 'encensoir') || str_contains($slug, 'parfum-de-chambre')) {
            return rand(5000, 50000); // Parfums et diffuseurs : 5k à 50k FCFA
        } elseif (str_contains($slug, 'lampes-de-chevet')) {
            return rand(8000, 80000); // Lampes de chevet : 8k à 80k FCFA
        } elseif (str_contains($slug, 'decorations-murales')) {
            return rand(5000, 100000); // Décorations murales : 5k à 100k FCFA
        } elseif (str_contains($slug, 'bougies-de-cire')) {
            return rand(2000, 15000); // Bougies : 2k à 15k FCFA
        } elseif (str_contains($slug, 'fleurs')) {
            return rand(3000, 50000); // Fleurs : 3k à 50k FCFA
        } elseif (str_contains($slug, 'pots-de-fleurs')) {
            return rand(2000, 30000); // Pots de fleurs : 2k à 30k FCFA
        } elseif (str_contains($slug, 'chafing-dish')) {
            return rand(25000, 150000); // Chafing dish : 25k à 150k FCFA
        } elseif (str_contains($slug, 'ceinture-de-bain')) {
            return rand(3000, 25000); // Ceinture de bain : 3k à 25k FCFA
        }
        
        // Prix selon le type de catégorie principale
        if (str_contains($categoryName, 'ordinateur') || str_contains($categoryName, 'smartphone') || str_contains($categoryName, 'électronique')) {
            return rand(50000, 2000000); // Électronique : 50k à 2M FCFA
        } elseif (str_contains($categoryName, 'cuisine') && (str_contains($categoryName, 'machine') || str_contains($categoryName, 'électroménager'))) {
            return rand(80000, 1500000); // Électroménager : 80k à 1.5M FCFA
        } elseif (str_contains($categoryName, 'maison') || str_contains($categoryName, 'meuble') || str_contains($categoryName, 'chambre')) {
            return rand(15000, 800000); // Mobilier : 15k à 800k FCFA
        } elseif (str_contains($categoryName, 'vêtements') || str_contains($categoryName, 'mode')) {
            return rand(5000, 150000); // Vêtements : 5k à 150k FCFA
        } elseif (str_contains($categoryName, 'hygiène') || str_contains($categoryName, 'beauté')) {
            return rand(3000, 50000); // Hygiène/Beauté : 3k à 50k FCFA
        } elseif (str_contains($categoryName, 'jouet')) {
            return rand(2000, 50000); // Jouets : 2k à 50k FCFA
        } elseif (str_contains($categoryName, 'livre')) {
            return rand(2000, 25000); // Livres : 2k à 25k FCFA
        } elseif (str_contains($categoryName, 'audio') || str_contains($categoryName, 'casque') || str_contains($categoryName, 'écouteur')) {
            return rand(5000, 300000); // Audio : 5k à 300k FCFA
        } elseif (str_contains($categoryName, 'chargeur') || str_contains($categoryName, 'accessoire')) {
            return rand(2000, 50000); // Accessoires : 2k à 50k FCFA
        } elseif (str_contains($categoryName, 'jardin')) {
            return rand(5000, 200000); // Jardinage : 5k à 200k FCFA
        } else {
            return rand(5000, 200000); // Par défaut : 5k à 200k FCFA
        }
    }
}
