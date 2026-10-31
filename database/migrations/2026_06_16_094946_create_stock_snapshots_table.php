<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->decimal('quantity', 12, 2);

            $table->date('snapshot_date');

            $table->timestamps();

            $table->index(['product_id', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_snapshots');
    }
};