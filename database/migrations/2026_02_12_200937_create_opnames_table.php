<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('opnames', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('system_qty', 10, 2);
            $table->decimal('physical_qty', 10, 2);
            $table->decimal('difference_qty', 10, 2);
            $table->enum('difference_type', ['kurang', 'lebih', 'sama'])->default('sama');
            $table->text('notes')->nullable();
            $table->timestamp('opname_date');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['draft', 'approved'])->default('draft')->after('difference_type');

            $table->timestamps();

            // Indexes
            $table->index(['code', 'warehouse_id', 'product_id', 'opname_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opnames');
    }
};
