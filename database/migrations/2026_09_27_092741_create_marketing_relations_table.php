<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_relations', function (Blueprint $table) {

            $table->id();

            // Stakeholder Type:
            // 2 = Dealer
            // 3 = Agent
            // 5 = Employee
            $table->unsignedTinyInteger('parent_type');
            $table->unsignedBigInteger('parent_id');

            $table->unsignedTinyInteger('child_type');
            $table->unsignedBigInteger('child_id');

            // 1 = Active, 0 = Cancelled
            $table->boolean('status')->default(1);

            // Who created the relation
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Prevent duplicate relation
            $table->unique(
                [
                    'parent_type',
                    'parent_id',
                    'child_type',
                    'child_id',
                ],
                'marketing_rel_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_relations');
    }
};