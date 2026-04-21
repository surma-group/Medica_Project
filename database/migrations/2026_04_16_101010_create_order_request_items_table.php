<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_request_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_request_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('product_name');
            $table->string('strength')->nullable();

            // ✅ UPDATED: unit_id instead of unit string
            $table->foreignId('unit_id')
                  ->constrained('units')
                  ->onDelete('restrict');

            $table->integer('quantity');

            // ❗ optional (recommend remove)
            $table->boolean('status')->default(1)
                  ->comment('1=active, 0=inactive');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_request_items');
    }
};