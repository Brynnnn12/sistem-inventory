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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('previous_qty', 10, 2);
            $table->decimal('new_qty', 10, 2);
            $table->decimal('change_qty', 10, 2);
            $table->enum('reference_type', ['inbound', 'outbound', 'mutation_sent', 'mutation_received', 'adjustment', 'opname']);
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_code', 50);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['stock_id', 'created_at']);
            $table->index('warehouse_id');
            $table->index('product_id');
            $table->index('reference_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
