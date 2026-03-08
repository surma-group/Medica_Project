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
        Schema::create('master_account', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
            $table->foreignId('auto_master_account_id')
                ->nullable()
                ->constrained('auto_master_account')
                ->nullOnDelete();
            $table->tinyInteger('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_account');
    }
};
