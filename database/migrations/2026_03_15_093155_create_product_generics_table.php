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
        Schema::create('product_generics', function (Blueprint $table) {
            $table->id();

            $table->string('generic_id')->nullable()->unique();
            $table->string('generic_name');

            $table->longText('precaution')->nullable();
            $table->longText('indication')->nullable();
            $table->longText('contra_indication')->nullable();
            $table->longText('dose')->nullable();
            $table->longText('side_effect')->nullable();
            $table->longText('mode_of_action')->nullable();
            $table->longText('interaction')->nullable();

            $table->unsignedBigInteger('pregnancy_category_id')->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes();

            // Optional: If you have pregnancy_categories table
            // $table->foreign('pregnancy_category_id')
            //       ->references('id')
            //       ->on('pregnancy_categories')
            //       ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_generics');
    }
};