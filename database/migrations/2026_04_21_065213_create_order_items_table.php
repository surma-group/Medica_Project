<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // order relation
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // product relation
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // pricing
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);

            // optional unit
            $table->unsignedBigInteger('unit_id')->nullable();

            // status
            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=active, 0=inactive');

            $table->timestamps();

            // unit relation
            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};