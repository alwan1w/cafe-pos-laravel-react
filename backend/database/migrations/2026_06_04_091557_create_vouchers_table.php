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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('type', ['fixed', 'percent']);
            $table->bigInteger('discount_value');
            $table->bigInteger('min_purchase')->default(0);
            $table->bigInteger('max_discount')->nullable(); // Untuk limit tipe persentase
            $table->integer('quota')->nullable(); // Null = unlimited
            $table->integer('used_count')->default(0);
            $table->dateTime('expired_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
