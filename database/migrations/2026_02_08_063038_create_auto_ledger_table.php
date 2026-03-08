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
        Schema::create('auto_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_charts_accounts_id')
                ->constrained('auto_charts_accounts')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_ledger');
    }
};
