
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
        Schema::create('marketing_area_scopes', function (Blueprint $table) {
            $table->id();

            // Parent assignment
            $table->foreignId('assignment_id')
                ->constrained('marketing_area_assignments')
                ->cascadeOnDelete();

            // country, division, district, upazila
            $table->string('scope_type', 20);

            // ID from the corresponding geographic table
            $table->unsignedBigInteger('scope_id');

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

            // Prevent duplicate scope within the same assignment
            $table->unique(
                ['assignment_id', 'scope_type', 'scope_id'],
                'marketing_area_scope_unique'
            );

            $table->index(
                ['scope_type', 'scope_id'],
                'marketing_area_scope_lookup_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_area_scopes');
    }
};
