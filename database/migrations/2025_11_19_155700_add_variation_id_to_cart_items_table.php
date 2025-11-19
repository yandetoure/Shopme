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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variation_id')->nullable()->after('product_id');
            $table->json('selected_attributes')->nullable()->after('variation_id'); // Sauvegarder les attributs sélectionnés
            $table->dropUnique(['user_id', 'product_id']); // Supprimer l'unicité car on peut avoir plusieurs variations du même produit
            $table->unique(['user_id', 'product_id', 'variation_id']); // Unique par produit et variation
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variation_id')->nullable()->after('product_id');
            $table->json('selected_attributes')->nullable()->after('variation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id', 'variation_id']);
            $table->unique(['user_id', 'product_id']);
            $table->dropColumn(['variation_id', 'selected_attributes']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variation_id', 'selected_attributes']);
        });
    }
};
