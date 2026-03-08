<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('time_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. Asia/Dhaka
            $table->string('label');         // e.g. (GMT+06:00) Dhaka
            $table->string('utc_offset');    // +06:00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_zones');
    }
};
