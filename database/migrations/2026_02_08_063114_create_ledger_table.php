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
        Schema::create('ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_account_id')
                ->constrained('charts_accounts')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('code');
            $table->tinyInteger('type');
            $table->boolean('for_income')->default(false);
            $table->boolean('for_expense')->default(false);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger');
    }
};
