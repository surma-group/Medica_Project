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
        Schema::create('voucher_entry', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->tinyInteger('type');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('time')->useCurrent();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_entry');
    }
};
