<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {

            $table->unsignedBigInteger('ledger_id')
                ->nullable()
                ->after('user_id');

            $table->foreign('ledger_id')
                ->references('id')
                ->on('ledger')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {

            $table->dropForeign(['ledger_id']);
            $table->dropColumn('ledger_id');
        });
    }
};