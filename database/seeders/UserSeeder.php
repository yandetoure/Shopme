<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les rôles
        $adminRole = Role::where('name', 'admin')->first();
        $vendeurRole = Role::where('name', 'vendeur')->first();
        $clientRole = Role::where('name', 'client')->first();

        // Créer l'administrateur
        $admin = User::firstOrCreate(
            ['email' => 'admin@shopme.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'phone' => '+33 1 23 45 67 89',
                'address' => '123 Rue de Paris, 75001 Paris',
            ]
        );
        if ($adminRole && !$admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        // Créer un vendeur
        $vendeur = User::firstOrCreate(
            ['email' => 'vendeur@shopme.com'],
            [
                'name' => 'Vendeur Test',
                'password' => Hash::make('password'),
                'phone' => '+33 1 98 76 54 32',
                'address' => '456 Avenue du Commerce, 75002 Paris',
            ]
        );
        if ($vendeurRole && !$vendeur->hasRole($vendeurRole)) {
            $vendeur->assignRole($vendeurRole);
        }

        // Créer un client
        $client = User::firstOrCreate(
            ['email' => 'client@shopme.com'],
            [
                'name' => 'Jean Dupont',
                'password' => Hash::make('password'),
                'phone' => '+33 6 12 34 56 78',
                'address' => '45 Avenue des Champs, 75008 Paris',
            ]
        );
        if ($clientRole && !$client->hasRole($clientRole)) {
            $client->assignRole($clientRole);
        }
    }
}
