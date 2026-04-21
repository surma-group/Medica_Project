<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // customer relation
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            // order number (unique)
            $table->string('order_number')->unique();

            // totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            $table->tinyInteger('payment_method')->default(1)->comment('1=cod');

            // status (same pattern as order_requests)
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=pending,1=completed,2=processing,3=cancelled');

            // note
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
