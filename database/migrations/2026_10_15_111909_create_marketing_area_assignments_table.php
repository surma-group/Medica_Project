
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
        Schema::create('marketing_area_assignments', function (Blueprint $table) {
            $table->id();

            // Employee = 1, Dealer = 2, Agent = 3
            $table->unsignedTinyInteger('member_type');
            $table->unsignedBigInteger('member_id');

            // Created by
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Last updated by
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // One assignment per member
            $table->unique(
                ['member_type', 'member_id'],
                'marketing_area_member_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_area_assignments');
    }
};
