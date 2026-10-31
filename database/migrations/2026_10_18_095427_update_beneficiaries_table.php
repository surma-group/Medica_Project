<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropIndex('beneficiaries_employee_id_foreign');
            $table->dropColumn('employee_id');

            $table->unsignedBigInteger('reference_id')
                ->after('id');

            $table->string('reference_type', 50)
                ->after('reference_id');

            $table->index('reference_id', 'beneficiaries_reference_id_index');
            $table->index('reference_type', 'beneficiaries_reference_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropIndex('beneficiaries_reference_id_index');
            $table->dropIndex('beneficiaries_reference_type_index');

            $table->dropColumn([
                'reference_id',
                'reference_type',
            ]);

            $table->unsignedBigInteger('employee_id')
                ->after('id');

            $table->index(
                'employee_id',
                'beneficiaries_employee_id_foreign'
            );

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();
        });
    }
};
