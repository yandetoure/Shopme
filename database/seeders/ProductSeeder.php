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
                        'description' => $this->generateFrenchDescription($category, $faker),
                        'short_description' => $this->generateFrenchShortDescription($category, $faker),
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

    /**
     * Générer une description courte en français
     */
    private function generateFrenchShortDescription(Category $category, $faker): string
    {
        $categoryName = strtolower($category->name);
        $descriptions = [
            'chambre' => [
                'Parfait pour embellir votre chambre avec style et confort.',
                'Ajoutez une touche d\'élégance à votre espace de détente.',
                'Conçu pour votre confort et votre bien-être quotidien.',
                'Décoration moderne pour créer une atmosphère chaleureuse.',
                'Qualité supérieure pour un repos optimal.',
            ],
            'cuisine' => [
                'Idéal pour faciliter votre quotidien en cuisine.',
                'Fonctionnel et pratique pour toutes vos préparations.',
                'Haute qualité pour des résultats professionnels.',
                'Révolutionnez votre expérience culinaire.',
                'Design moderne pour une cuisine contemporaine.',
            ],
            'toilette' => [
                'Transformez votre salle de bain en espace spa.',
                'Confort et élégance pour vos moments de détente.',
                'Qualité premium pour une hygiène optimale.',
                'Organisez votre espace avec style.',
                'Luxe et praticité pour votre bien-être.',
            ],
            'jardin' => [
                'Donnez vie à votre jardin avec nos produits de qualité.',
                'Créez un espace vert magnifique et accueillant.',
                'Parfait pour les amoureux de la nature.',
                'Durabilité et esthétique pour l\'extérieur.',
                'Transformez votre extérieur en paradis vert.',
            ],
            'maison' => [
                'Embellissez votre intérieur avec élégance.',
                'Qualité exceptionnelle pour votre foyer.',
                'Design contemporain pour un style unique.',
                'Créez une ambiance chaleureuse et accueillante.',
                'Fonctionnalité et esthétique pour votre maison.',
            ],
            'informatique' => [
                'Performance et fiabilité pour vos besoins technologiques.',
                'Technologie de pointe pour une productivité optimale.',
                'Innovation au service de votre productivité.',
                'Haute performance pour professionnels et particuliers.',
                'Solutions informatiques modernes et efficaces.',
            ],
            'vêtements' => [
                'Style et confort pour toutes les occasions.',
                'Tendance et qualité pour votre garde-robe.',
                'Mode actuelle avec une finition soignée.',
                'Confortable et élégant pour votre quotidien.',
                'Qualité textile supérieure pour un look impeccable.',
            ],
        ];

        foreach ($descriptions as $key => $descArray) {
            if (str_contains($categoryName, $key)) {
                return $descArray[array_rand($descArray)];
            }
        }

        return $faker->sentence(rand(8, 12));
    }

    /**
     * Générer une description détaillée en français
     */
    private function generateFrenchDescription(Category $category, $faker): string
    {
        $categoryName = strtolower($category->name);
        $slug = strtolower($category->slug);
        
        $descriptions = [
            'chambre' => [
                'Découvrez ce produit exceptionnel qui transformera votre chambre en un véritable havre de paix. Conçu avec des matériaux de haute qualité, il offre un confort optimal et une durabilité à toute épreuve. Son design élégant s\'intègre parfaitement dans tous les styles de décoration, du moderne au classique. Facile à entretenir, il vous accompagnera pendant de nombreuses années.',
                'Ce produit de qualité supérieure est spécialement conçu pour embellir votre espace de repos. Avec ses finitions soignées et son design raffiné, il apportera une touche d\'élégance à votre chambre. Pratique et fonctionnel, il répond à tous vos besoins en matière de confort et d\'organisation.',
            ],
            'cuisine' => [
                'Révolutionnez votre expérience culinaire avec ce produit innovant. Conçu pour faciliter votre quotidien en cuisine, il combine fonctionnalité et design moderne. Les matériaux de haute qualité garantissent une utilisation durable et efficace. Que vous soyez débutant ou chef expérimenté, ce produit répondra à toutes vos attentes.',
                'Ce produit de cuisine professionnel vous permet de réaliser vos recettes avec précision et facilité. Sa conception ergonomique et ses matériaux résistants en font un allié indispensable de votre cuisine. Facile à utiliser et à nettoyer, il vous fera gagner du temps au quotidien.',
            ],
            'toilette' => [
                'Transformez votre salle de bain en un véritable espace de bien-être avec ce produit haut de gamme. Conçu pour votre confort et votre hygiène, il allie esthétique moderne et fonctionnalité pratique. Les matériaux de qualité supérieure garantissent une longue durée de vie et une facilité d\'entretien.',
                'Découvrez le confort et l\'élégance avec ce produit premium pour votre salle de bain. Son design soigné et ses finitions de qualité apporteront une touche de luxe à votre espace. Pratique et résistant, il répond à tous vos besoins quotidiens en matière d\'hygiène et de bien-être.',
            ],
            'jardin' => [
                'Donnez vie à votre jardin avec ce produit de qualité exceptionnelle. Conçu pour l\'extérieur, il résiste aux intempéries tout en conservant son esthétique. Facile à installer et à entretenir, il vous permettra de créer un espace vert magnifique et accueillant. Parfait pour les amoureux de la nature et du jardinage.',
                'Ce produit extérieur transformera votre jardin en un véritable paradis végétal. Sa conception robuste et ses matériaux durables garantissent une utilisation longue durée. Esthétique et fonctionnel, il s\'intègre parfaitement dans tous les styles de jardinage, du moderne au traditionnel.',
            ],
            'maison' => [
                'Embellissez votre intérieur avec ce produit au design contemporain et élégant. Conçu pour créer une ambiance chaleureuse et accueillante, il s\'adapte à tous les styles de décoration. Les matériaux de qualité supérieure garantissent à la fois esthétique et durabilité. Facile à entretenir, il apportera une touche de sophistication à votre foyer.',
                'Ce produit d\'exception transformera votre espace de vie en un lieu raffiné et accueillant. Son design soigné et ses finitions de qualité apporteront une valeur ajoutée à votre intérieur. Fonctionnel et esthétique, il répond à tous vos besoins en matière de décoration et de confort.',
            ],
            'informatique' => [
                'Découvrez une expérience technologique exceptionnelle avec ce produit informatique de dernière génération. Conçu pour offrir des performances optimales, il répond aux besoins des professionnels comme des particuliers. Sa technologie de pointe garantit rapidité, fiabilité et efficacité dans toutes vos tâches quotidiennes.',
                'Ce produit informatique combine innovation et performance pour une productivité maximale. Avec ses caractéristiques techniques avancées et son design ergonomique, il vous accompagnera dans tous vos projets. Facile à utiliser et à configurer, il s\'adapte à tous vos besoins professionnels et personnels.',
            ],
            'vêtements' => [
                'Adoptez un style tendance et élégant avec ce produit de mode soigneusement conçu. Les textiles de qualité supérieure garantissent confort et durabilité. Son design actuel et ses finitions soignées en font un incontournable de votre garde-robe. Parfait pour toutes les occasions, il allie style et praticité.',
                'Ce produit de mode vous permettra d\'exprimer votre personnalité avec élégance. Conçu avec des matières de qualité et un souci du détail, il offre un confort optimal au quotidien. Son style intemporel et sa finition impeccable en font un investissement durable pour votre garde-robe.',
            ],
        ];

        foreach ($descriptions as $key => $descArray) {
            if (str_contains($categoryName, $key) || str_contains($slug, $key)) {
                return $descArray[array_rand($descArray)];
            }
        }

        // Description générique en français si aucune correspondance
        return 'Découvrez ce produit de qualité supérieure, conçu pour répondre à tous vos besoins. ' .
               'Avec ses caractéristiques exceptionnelles et son design soigné, il vous offrira satisfaction et performance. ' .
               'Les matériaux de haute qualité garantissent une utilisation durable et efficace. ' .
               'Facile à utiliser et à entretenir, ce produit deviendra rapidement indispensable dans votre quotidien.';
    }
}
