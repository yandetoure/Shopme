<?php declare(strict_types=1); 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Code promo (ex: "PROMO2024")
            $table->string('name'); // Nom du coupon (ex: "Réduction de 10%")
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']); // Pourcentage ou montant fixe
            $table->decimal('value', 10, 2); // Valeur (10% ou 5000 FCFA)
            $table->decimal('minimum_amount', 10, 2)->nullable(); // Montant minimum de commande
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Remise maximum (pour les %)
            $table->integer('usage_limit')->nullable(); // Limite d'utilisation totale
            $table->integer('usage_limit_per_user')->nullable(); // Limite par utilisateur
            $table->integer('used_count')->default(0); // Nombre d'utilisations
            $table->date('valid_from')->nullable(); // Date de début
            $table->date('valid_until')->nullable(); // Date de fin
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
            $table->index('valid_from');
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
