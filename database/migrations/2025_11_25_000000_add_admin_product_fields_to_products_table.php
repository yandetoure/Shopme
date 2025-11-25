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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->nullable()->after('price');
            $table->string('supplier_name')->nullable()->after('purchase_price');
            $table->enum('sale_unit', ['unit', 'dozen'])->default('unit')->after('supplier_name');
            $table->boolean('is_discovery')->default(false)->after('featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'supplier_name', 'sale_unit', 'is_discovery']);
        });
    }
};

