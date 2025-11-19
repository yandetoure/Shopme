<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Créer les catégories parentes avec leurs sous-catégories
        $categoriesData = [
            // Chambre
            'Chambre' => [
                'Lit et literie',
                'Armoires et rangements',
                'Meubles de chambre',
                'Décoration chambre',
                'Éclairage chambre',
                'Lampes de chevet',
                'Parfums de chambre',
                'Diffuseurs',
                'Encensoirs'
            ],
            
            // Cuisine
            'Cuisine' => [
                'Ustensiles de cuisine',
                'Électroménager',
                'Machine à laver',
                'Sèche-linge',
                'Micro-ondes',
                'Machine à café',
                'Chafing dish',
                'Décoration cuisine',
                'Rangement cuisine',
                'Vaisselle et couverts'
            ],
            
            // Toilette
            'Toilette' => [
                'Accessoires salle de bain',
                'Linge de toilette',
                'Ceinture de bain',
                'Rangement salle de bain',
                'Organisation salle de bain',
                'Décoration salle de bain',
                'Miroirs et accessoires'
            ],
            
            // Jardin
            'Jardin' => [
                'Outils de jardinage',
                'Plantes et fleurs',
                'Fleurs',
                'Pots de fleurs',
                'Mobilier jardin',
                'Décoration jardin',
                'Éclairage extérieur',
                'Arrosage et irrigation'
            ],
            
            // Maison
            'Maison' => [
                'Meubles',
                'Décoration',
                'Décorations murales',
                'Luminaire',
                'Textile maison',
                'Rangement maison',
                'Bougies de cire'
            ],
            
            // Lingeries et astuces
            'Lingeries et astuces' => [
                'Lingerie femme',
                'Lingerie homme',
                'Bas et collants',
                'Accessoires intimes',
                'Astuces beauté'
            ],
            
            // Jouets
            'Jouets' => [
                'Puzzles',
                'Jeux de société',
                'Poupées',
                'Voitures jouets',
                'Jeux éducatifs',
                'Jouets bébé'
            ],
            
            // Informatique
            'Informatique' => [
                'Accessoires informatique',
                'Périphériques',
                'Composants',
                'Logiciels',
                'Stockage'
            ],
            
            // Ordinateur
            'Ordinateur' => [
                'Ordinateurs portables',
                'Ordinateurs de bureau',
                'Moniteurs',
                'Claviers et souris',
                'Accessoires ordinateur'
            ],
            
            // Téléphones
            'Téléphones' => [
                'Smartphones',
                'Téléphones fixes',
                'Accessoires téléphones',
                'Étuis et coques',
                'Protection écran'
            ],
            
            // Audio et son
            'Audio et son' => [
                'Enceintes',
                'Amplificateurs',
                'Hi-Fi',
                'Radio',
                'Accessoires audio'
            ],
            
            // Écouteurs
            'Écouteurs' => [
                'Écouteurs filaires',
                'Écouteurs Bluetooth',
                'Écouteurs intra-auriculaires',
                'Écouteurs circum-auriculaires',
                'Écouteurs sport'
            ],
            
            // Casque
            'Casque' => [
                'Casques audio',
                'Casques Bluetooth',
                'Casques gaming',
                'Casques professionnels',
                'Accessoires casques'
            ],
            
            // Chargeurs
            'Chargeurs' => [
                'Chargeurs USB',
                'Chargeurs sans fil',
                'Chargeurs rapides',
                'Batteries externes',
                'Câbles de charge'
            ],
            
            // Catégories supplémentaires
            'Électronique' => [
                'Smartphones',
                'Ordinateurs portables',
                'Tablettes',
                'Accessoires électroniques'
            ],
            
            'Vêtements' => [
                'Homme',
                'Femme',
                'Enfant',
                'Chaussures',
                'Accessoires mode'
            ],
            
            'Sport' => [
                'Fitness',
                'Running',
                'Football',
                'Basketball'
            ],
            
            'Beauté' => [
                'Cosmétiques',
                'Soins visage',
                'Parfums',
                'Accessoires beauté'
            ],
            
            // Hygiène et soins
            'Hygiène et soins' => [
                'Shampoing',
                'Après-shampoing',
                'Démêlant',
                'Lait de corps',
                'Savons et gels douche',
                'Déodorants',
                'Soins cheveux',
                'Soins corps',
                'Produits bébé'
            ],
            
            // Rangement
            'Rangement' => [
                'Rangement chaussures',
                'Rangement cuisine',
                'Rangement salle de bain',
                'Rangement chambre',
                'Rangement maison',
                'Accessoires rangement'
            ],
            
            'Livres' => [
                'Romans',
                'BD & Mangas',
                'Scolaire',
                'Livres jeunesse'
            ],
            
            'Alimentation' => [
                'Bio',
                'Épicerie',
                'Boissons',
                'Surgelés'
            ]
        ];

        $sortOrder = 1;
        $totalCategories = 0;

        foreach ($categoriesData as $parentName => $subCategories) {
            // Créer la catégorie parente
            $parentCategory = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'description' => $faker->sentence(8),
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
            
            $totalCategories++;

            // Créer les sous-catégories
            foreach ($subCategories as $subIndex => $subName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($subName)],
                    [
                        'name' => $subName,
                        'parent_id' => $parentCategory->id,
                        'description' => $faker->sentence(6),
                        'is_active' => true,
                        'sort_order' => $subIndex + 1,
                    ]
                );
                $totalCategories++;
            }
        }

        $this->command->info("{$totalCategories} catégories et sous-catégories créées avec succès.");
    }
}
