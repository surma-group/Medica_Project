<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('payment_type')->comment('1=Earning, 2=Deduction');
            $table->tinyInteger('amount_type')->comment('1=Flat, 2=Percentage');
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps(); // Laravel common fields: created_at, updated_at
            $table->softDeletes(); // Optional: for soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
