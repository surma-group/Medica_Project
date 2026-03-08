<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {

            $table->timestamp('approve_at')
                ->nullable()
                ->after('approve_by');

            $table->unsignedBigInteger('paid_by')
                ->nullable()
                ->after('approve_at');

            $table->timestamp('paid_at')
                ->nullable()
                ->after('paid_by');
        });
    }

    public function down(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropColumn([
                'approve_at',
                'paid_by',
                'paid_at',
            ]);
        });
    }
};
