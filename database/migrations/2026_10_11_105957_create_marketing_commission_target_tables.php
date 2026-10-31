
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('marketing_member_targets', function (Blueprint $table) {
            $table->id();

            // Employee, Dealer or Agent
            $table->unsignedTinyInteger('member_type');

            // Employee ID or Partner ID
            $table->unsignedBigInteger('member_id');

            // Daily, Weekly, Monthly or Yearly
            $table->unsignedTinyInteger('period_type');

            // Flat or Percentage
            $table->unsignedTinyInteger('target_type');

            // Sales target amount
            $table->decimal('target_amount', 15, 2);

            $table->timestamps();

            // One target per member for each period
            $table->unique(
                ['member_type', 'member_id', 'period_type'],
                'marketing_targets_member_period_unique'
            );

            $table->index(
                ['member_type', 'member_id'],
                'marketing_targets_member_index'
            );
        });

        Schema::create('marketing_member_commissions', function (Blueprint $table) {
            $table->id();

            // Employee, Dealer or Agent
            $table->unsignedTinyInteger('member_type');

            // Employee ID or Partner ID
            $table->unsignedBigInteger('member_id');

            // Flat or Percentage
            $table->unsignedTinyInteger('commission_type');

            // Commission amount
            $table->decimal('commission_amount', 15, 2);

            $table->timestamps();

            // One commission setting per member
            $table->unique(
                ['member_type', 'member_id'],
                'marketing_commissions_member_unique'
            );

            $table->index(
                ['member_type', 'member_id'],
                'marketing_commissions_member_index'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('marketing_member_commissions');

        Schema::dropIfExists('marketing_member_targets');
    }
};