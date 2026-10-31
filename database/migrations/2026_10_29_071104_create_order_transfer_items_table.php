<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_transfer_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_transfer_id');
            $table->unsignedBigInteger('order_item_id');

            $table->decimal('quantity_sent', 15, 3);
            $table->decimal('quantity_received', 15, 3)->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('order_transfer_id')
                ->references('id')
                ->on('order_transfers')
                ->cascadeOnDelete();

            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
                ->restrictOnDelete();

            // Prevent duplicate order items in the same transfer
            $table->unique(
                ['order_transfer_id', 'order_item_id'],
                'transfer_item_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_transfer_items');
    }
};
