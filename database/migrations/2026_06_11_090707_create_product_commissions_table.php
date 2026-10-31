<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_commissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('user_type_id')
                ->constrained('user_type')
                ->cascadeOnDelete();

            $table->decimal('commission', 10, 2);

            $table->tinyInteger('commission_type')->default(1);
            // 1 = Percentage, 2 = Fixed

            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->index('product_id');
            $table->index('user_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_commissions');
    }
};
