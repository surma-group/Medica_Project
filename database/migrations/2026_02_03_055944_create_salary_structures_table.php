<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 12, 2)->default(0);

            $table->decimal('total_earning', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // Optional FK
            // $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};

