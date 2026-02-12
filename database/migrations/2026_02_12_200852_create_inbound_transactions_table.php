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
        Schema::create('inbound_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2)->storedAs('quantity * unit_price');
            $table->date('received_date');
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['code', 'warehouse_id', 'supplier_id', 'product_id', 'received_date', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_transactions');
    }
};
