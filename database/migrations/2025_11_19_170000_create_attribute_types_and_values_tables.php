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
        Schema::create('attribute_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Couleur', 'Taille', 'Matériau', etc.
            $table->string('slug')->unique(); // 'couleur', 'taille', 'materiau'
            $table->string('type')->default('text'); // 'text', 'color', 'image', 'select'
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_type_id');
            $table->string('value'); // 'Rouge', 'XL', 'Coton', etc.
            $table->string('color_code')->nullable(); // Code hexadécimal pour les couleurs
            $table->string('image')->nullable(); // Image spécifique pour cette valeur (ex: couleur)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('attribute_type_id')->references('id')->on('attribute_types')->onDelete('cascade');
            $table->index('attribute_type_id');
            $table->index('is_active');
            $table->unique(['attribute_type_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attribute_types');
    }
};

