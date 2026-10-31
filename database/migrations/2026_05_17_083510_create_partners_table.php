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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');
            $table->string('shop_name');

            // dealer=1, agent=2
            $table->tinyInteger('type')->default(1);

            $table->string('phone')->unique();
            $table->string('emergency_contact')->nullable();

            $table->string('email')->nullable();
            $table->text('address')->nullable();

            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete();

            $table->string('postcode')->nullable();

            // Files
            $table->string('image')->nullable();
            $table->string('nid_file')->nullable();
            $table->string('trade_license_file')->nullable();

            // Trade license number
            $table->string('trade_license')->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};