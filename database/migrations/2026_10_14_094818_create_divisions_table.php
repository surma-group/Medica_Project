<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->string('name_en');
            $table->string('name_bn');
            $table->string('code', 10)->nullable()->unique();

            $table->timestamps();

            $table->unique(['country_id', 'name_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};