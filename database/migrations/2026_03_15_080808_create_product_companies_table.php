<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->integer('company_order')->nullable();
            $table->tinyInteger('status')->default(1); // 1 = active , 0 = inactive
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // optional common laravel field
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_companies');
    }
};