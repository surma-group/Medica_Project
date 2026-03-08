<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('base_unit_id')->nullable()->after('image');
            $table->unsignedBigInteger('secondary_unit_id')->nullable()->after('base_unit_id');
            $table->decimal('conversion_rate', 10, 2)->nullable()->after('secondary_unit_id');

            // Optional: add foreign keys
            $table->foreign('base_unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('secondary_unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_unit', 'secondary_unit', 'conversion_rate']);
        });
    }
};
