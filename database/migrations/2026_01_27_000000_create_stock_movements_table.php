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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->enum('movement_type', ['in', 'out', 'adjustment', 'reserved', 'released'])->default('out');
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reference')->nullable(); // Reference number (invoice, quote, purchase, etc)
            $table->string('reference_type')->nullable(); // Type: invoice, quote, purchase
            $table->foreignId('reference_id')->nullable(); // ID of the referenced record
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
