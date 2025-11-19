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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_rate_id')->nullable()->after('shipping');
            $table->unsignedBigInteger('coupon_id')->nullable()->after('shipping_rate_id');
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->decimal('discount', 10, 2)->default(0)->after('coupon_code'); // Montant de la remise
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount'); // Montant après remise (avant shipping)
            
            $table->foreign('shipping_rate_id')->references('id')->on('shipping_rates')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->index('shipping_rate_id');
            $table->index('coupon_id');
        });

        // Créer une table pour suivre l'utilisation des coupons par utilisateur
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['coupon_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_rate_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropIndex(['shipping_rate_id']);
            $table->dropIndex(['coupon_id']);
            $table->dropColumn(['shipping_rate_id', 'coupon_id', 'coupon_code', 'discount', 'discount_amount']);
        });
    }
};
