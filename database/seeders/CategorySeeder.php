<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des catégories parentes
        $electronics = Category::firstOrCreate(
            ['slug' => Str::slug('Électronique')],
            [
                'name' => 'Électronique',
                'description' => 'Tous les produits électroniques',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $clothing = Category::firstOrCreate(
            ['slug' => Str::slug('Vêtements')],
            [
                'name' => 'Vêtements',
                'description' => 'Mode et vêtements',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $home = Category::firstOrCreate(
            ['slug' => Str::slug('Maison')],
            [
                'name' => 'Maison',
                'description' => 'Décoration et ameublement',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Créer des sous-catégories pour Électronique
        $smartphones = Category::firstOrCreate(
            ['slug' => Str::slug('Smartphones')],
            [
                'name' => 'Smartphones',
                'parent_id' => $electronics->id,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $laptops = Category::firstOrCreate(
            ['slug' => Str::slug('Ordinateurs portables')],
            [
                'name' => 'Ordinateurs portables',
                'parent_id' => $electronics->id,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $tablets = Category::firstOrCreate(
            ['slug' => Str::slug('Tablettes')],
            [
                'name' => 'Tablettes',
                'parent_id' => $electronics->id,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Créer des sous-catégories pour Vêtements
        $men = Category::firstOrCreate(
            ['slug' => Str::slug('Homme')],
            [
                'name' => 'Homme',
                'parent_id' => $clothing->id,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $women = Category::firstOrCreate(
            ['slug' => Str::slug('Femme')],
            [
                'name' => 'Femme',
                'parent_id' => $clothing->id,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $kids = Category::firstOrCreate(
            ['slug' => Str::slug('Enfant')],
            [
                'name' => 'Enfant',
                'parent_id' => $clothing->id,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Créer des sous-catégories pour Maison
        $furniture = Category::firstOrCreate(
            ['slug' => Str::slug('Meubles')],
            [
                'name' => 'Meubles',
                'parent_id' => $home->id,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $decoration = Category::firstOrCreate(
            ['slug' => Str::slug('Décoration')],
            [
                'name' => 'Décoration',
                'parent_id' => $home->id,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
