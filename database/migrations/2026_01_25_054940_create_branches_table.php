<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('branch_code')->unique();
            $table->string('branch_name');
            $table->boolean('is_head_office')->default(0);
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->tinyText('address')->nullable();
            $table->foreignId('district')->constrained('districts')->onDelete('cascade');
            $table->date('opening_date')->nullable();
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
