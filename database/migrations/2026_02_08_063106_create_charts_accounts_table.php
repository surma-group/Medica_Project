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
        Schema::create('charts_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
            $table->foreignId('auto_charts_accounts_id')
                ->nullable()
                ->constrained('auto_charts_accounts')
                ->nullOnDelete();
            $table->foreignId('master_account_id')
                ->constrained('master_account')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charts_accounts');
    }
};
