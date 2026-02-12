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
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('from_warehouse')->constrained('warehouses');
            $table->foreignId('to_warehouse')->constrained('warehouses');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 10, 2);
            $table->decimal('received_qty', 10, 2)->default(0);
            $table->decimal('damaged_qty', 10, 2)->default(0);
            $table->enum('status', ['dikirim', 'diterima', 'ditolak', 'selesai'])->default('dikirim');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['code', 'from_warehouse', 'to_warehouse', 'product_id', 'status', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
