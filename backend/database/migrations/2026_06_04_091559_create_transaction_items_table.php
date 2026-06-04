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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            // Snapshot Data (Agar jika harga/nama produk diubah di master data, riwayat transaksi masa lalu tidak berubah)
            $table->string('product_name');
            $table->enum('product_type', ['food', 'drink']);
            $table->integer('qty');
            $table->bigInteger('unit_price');
            $table->bigInteger('subtotal'); // unit_price x qty
            $table->text('notes')->nullable(); // Catatan khusus per item, misal: "jangan pedas"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
