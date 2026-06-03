<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone', 20);
            $table->foreignId('table_id')->constrained('tables')->restrictOnDelete();
            $table->integer('guest_count');
            $table->dateTime('reserved_at'); // Waktu kedatangan yang di-booking
            $table->dateTime('expired_at');  // reserved_at + 60 menit
            $table->text('notes')->nullable();

            // Informasi DP
            $table->bigInteger('dp_amount')->default(20000);
            $table->enum('dp_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->nullable();

            // Status Booking
            $table->enum('status', ['pending', 'confirmed', 'arrived', 'expired', 'cancelled'])->default('pending');
            $table->timestamps();

            // Indexing untuk mempercepat pencarian jadwal dan cron job
            $table->index('reserved_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
