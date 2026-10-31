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
        Schema::table('withdraw_requests', function (Blueprint $table) {
            // Remove old employee relationship
            $table->dropForeign('withdraw_requests_employee_id_foreign');
            $table->dropColumn('employee_id');

            // Add common reference relationship
            $table->unsignedBigInteger('reference_id')->after('id');
            $table->string('reference_type', 50)->after('reference_id');

            $table->index('reference_id');
            $table->index('reference_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            // Remove new columns and indexes
            $table->dropIndex(['reference_id']);
            $table->dropIndex(['reference_type']);
            $table->dropColumn(['reference_id', 'reference_type']);

            // Restore old employee relationship
            $table->unsignedBigInteger('employee_id')->after('id');

            $table->foreign('employee_id', 'withdraw_requests_employee_id_foreign')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }
};
