<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_requests', function (Blueprint $table) {
            $table->id();

            // customer relation
            $table->foreignId('customer_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('order_number')->unique();

            // 1 = general, 2 = prescription
            $table->tinyInteger('type')->default(1)
                  ->comment('1=general, 2=prescription');

            // prescription optional
            $table->string('prescription_file')->nullable();
            $table->text('prescription_description')->nullable();

            $table->integer('total_items')->default(0);

            // 0=pending,1=completed,2=processing,3=cancelled
            $table->tinyInteger('status')->default(0)
                  ->comment('0=pending,1=completed,2=processing,3=cancelled');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_requests');
    }
};