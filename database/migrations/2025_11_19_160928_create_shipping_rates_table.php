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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du tarif (ex: "Standard", "Express", "Gratuit")
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Prix en FCFA
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Montant minimum de commande pour ce tarif
            $table->decimal('max_order_amount', 10, 2)->nullable(); // Montant maximum (pour gratuit par exemple)
            $table->boolean('is_free')->default(false); // Livraison gratuite
            $table->integer('estimated_days')->nullable(); // Nombre de jours estimés
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
