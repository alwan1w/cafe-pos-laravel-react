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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice', 50)->unique();
            $table->string('customer_name'); // Kita simpan namanya langsung sebagai snapshot
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->string('table_number', 10)->nullable(); // Snapshot
            $table->foreignUuid('cashier_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();

            // Kalkulasi Uang
            $table->bigInteger('subtotal');
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('grand_total');

            // Pembayaran
            $table->enum('payment_method', ['cash', 'qris']);
            $table->bigInteger('payment_amount'); // Uang yang diterima kasir
            $table->bigInteger('change_amount')->default(0); // Kembalian

            // Status
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexing untuk dashboard analitik nanti
            $table->index('created_at');
            $table->index('status');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
