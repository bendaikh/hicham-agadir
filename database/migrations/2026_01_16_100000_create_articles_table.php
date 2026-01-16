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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // e.g., 100.27000
            $table->string('designation'); // e.g., PMMA COULE ALTUGLAS - DIFFUSANT - BLANC
            $table->string('category')->nullable(); // e.g., PMMA, Vitrage, etc.
            $table->string('dimensions')->nullable(); // e.g., 3X2M
            $table->string('thickness')->nullable(); // e.g., 3MM, 6MM, 15MM
            $table->string('color')->nullable(); // e.g., BLANC, TRANSPARENT
            $table->string('type')->nullable(); // e.g., DIFFUSANT, TRANSPARENT
            $table->string('unit')->default('Plaque'); // Plaque, M², ml, unité
            $table->decimal('selling_price', 15, 2)->default(0); // Prix de vente
            $table->decimal('price_per_unit', 15, 2)->nullable(); // Prix par unité (M², etc.)
            $table->decimal('surface_area', 10, 4)->nullable(); // Surface en M² (e.g., 6.19)
            $table->integer('stock_quantity')->default(0); // Quantité en stock
            $table->integer('min_stock')->default(0); // Stock minimum (alerte)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
