<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_salaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('salary_month'); // YYYY-MM-01

            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('total_earning', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);

            $table->enum('status', ['pending', 'paid'])->default('pending');

            $table->foreignId('generated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // Prevent duplicate salary generation per month
            $table->unique(['employee_id', 'salary_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_salaries');
    }
};
