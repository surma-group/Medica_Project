<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            // Add admin_note after note
            $table->text('admin_note')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropColumn('admin_note');
        });
    }
};