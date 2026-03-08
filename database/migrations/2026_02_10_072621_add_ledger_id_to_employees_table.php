<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add ledger_id column after 'user_id'
            $table->foreignId('ledger_id')->nullable()->constrained('ledger')->nullOnDelete()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['ledger_id']);
            $table->dropColumn('ledger_id');
        });
    }
};
