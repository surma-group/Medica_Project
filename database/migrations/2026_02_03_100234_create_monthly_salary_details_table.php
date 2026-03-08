<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_salary_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monthly_salary_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('salary_component_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('component_type', ['earning', 'deduction']);
            $table->tinyInteger('amount_type'); // 1=fixed, 2=percentage

            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('calculated_amount', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_salary_details');
    }
};
