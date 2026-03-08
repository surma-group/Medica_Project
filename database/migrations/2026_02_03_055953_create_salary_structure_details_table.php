<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_structure_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('salary_structure_id');
            $table->unsignedBigInteger('salary_component_id');

            $table->tinyInteger('component_type'); // 1=earning, 2=deduction
            $table->tinyInteger('amount_type');    // 1=fixed, 2=percentage

            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('calculated_amount', 12, 2)->default(0);

            $table->timestamps();

            // Optional FK
            // $table->foreign('salary_structure_id')->references('id')->on('salary_structures')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structure_details');
    }
};

