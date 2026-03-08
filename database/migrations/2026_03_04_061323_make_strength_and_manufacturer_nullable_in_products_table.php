<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('strength', 100)->nullable()->change();
            $table->string('manufacturer_name', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('strength', 100)->nullable(false)->change();
            $table->string('manufacturer_name', 255)->nullable(false)->change();
        });
    }
};