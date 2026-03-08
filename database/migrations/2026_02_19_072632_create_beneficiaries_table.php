<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();

            // Relation
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->cascadeOnDelete();

            // Bank info
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->tinyInteger('account_type')->nullable()->comment('1 = Savings, 2 = Current');
            $table->string('account_number')->nullable();

            // Mobile banking
            $table->string('bkash_number')->nullable();
            $table->string('rocket_number')->nullable();
            $table->string('nagad_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};

