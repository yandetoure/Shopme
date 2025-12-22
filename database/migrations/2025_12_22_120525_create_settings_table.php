<?php

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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Informations générales
            $table->string('site_name')->default('ShopMe');
            $table->string('site_email')->nullable();
            $table->string('logo')->nullable();
            $table->string('slogan')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email_contact')->nullable();
            
            // Couleurs du site
            $table->string('navbar_color')->default('#ffffff');
            $table->string('navbar_text_color')->default('#000000');
            $table->string('primary_color')->default('#f97316'); // orange
            $table->string('secondary_color')->default('#6b7280'); // gray
            $table->string('text_color')->default('#1f2937'); // dark gray
            $table->string('background_color')->default('#ffffff');
            
            // Typographie
            $table->string('font_family')->default('Inter, sans-serif');
            $table->string('heading_font')->nullable();
            
            // Paramètres financiers
            $table->string('currency')->default('FCFA');
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->decimal('default_shipping', 10, 2)->default(6550.00);
            
            // Réseaux sociaux
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
