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
        Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('status')->default('pending'); // pending, paid, shipped, delivered, cancelled
        $table->decimal('total_price', 12, 2);
        $table->string('shipping_address');
        $table->string('payment_method')->nullable(); // transfer, midtrans, dll
        $table->string('payment_status')->default('unpaid');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
