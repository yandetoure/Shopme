<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les permissions
        $permissions = [
            // Gestion des produits
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Gestion des catégories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Gestion des commandes
            'view orders',
            'manage orders',
            'cancel orders',
            
            // Gestion des utilisateurs
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Gestion des rôles et permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Vente
            'manage sales',
            'view sales reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendeurRole = Role::firstOrCreate(['name' => 'vendeur']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);

        // Assigner toutes les permissions au rôle admin
        $adminRole->givePermissionTo(Permission::all());

        // Assigner des permissions au rôle vendeur
        $vendeurRole->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'view categories',
            'view orders',
            'manage orders',
            'view sales reports',
        ]);

        // Le rôle client n'a pas besoin de permissions spéciales (accès public)
    }
}
