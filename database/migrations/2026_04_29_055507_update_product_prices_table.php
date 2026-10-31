<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {

            // rename column
            $table->renameColumn('price_type', 'user_type_id');

        });

        Schema::table('product_prices', function (Blueprint $table) {

            // change type + add foreign key
            $table->unsignedBigInteger('user_type_id')->change();

            $table->foreign('user_type_id')
                ->references('id')
                ->on('user_type')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {

            $table->dropForeign(['user_type_id']);

            $table->renameColumn('user_type_id', 'price_type');
        });
    }
};
