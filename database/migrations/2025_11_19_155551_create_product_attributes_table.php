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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name'); // 'Couleur', 'Taille', 'Matériau', etc.
            $table->string('slug'); // 'couleur', 'taille', 'materiau'
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->unique(['product_id', 'slug']);
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->string('value'); // 'Rouge', 'XL', 'Coton', etc.
            $table->string('color_code')->nullable(); // Code hexadécimal pour les couleurs
            $table->string('image')->nullable(); // Image spécifique pour cette valeur (ex: couleur)
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            $table->index('attribute_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
