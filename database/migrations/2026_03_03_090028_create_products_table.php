<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode', 50)->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->string('generic_name', 255)->nullable();
            $table->string('strength', 100);
            $table->string('manufacturer_name', 255);
            $table->string('image', 255)->nullable(); // Product image path
            $table->tinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};