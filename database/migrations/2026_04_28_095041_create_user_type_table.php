<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_type', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Agent, Dealer, Customer etc.

            // JSON field for flexible data storage
            $table->json('data')->nullable();

            // 1 = active, 0 = inactive
            $table->tinyInteger('status')->default(1);

            // Laravel common fields
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_type');
    }
};