<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('security_deposit_type')->nullable()->after('other_documents');
            $table->string('security_deposit_file')->nullable()->after('security_deposit_type');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'security_deposit_type',
                'security_deposit_file'
            ]);
        });
    }
};
