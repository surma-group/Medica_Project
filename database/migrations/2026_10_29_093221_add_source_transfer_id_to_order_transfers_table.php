<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_transfers', function (Blueprint $table) {
            $table->foreignId('source_transfer_id')
                ->nullable()
                ->after('order_id')
                ->constrained('order_transfers')
                ->restrictOnDelete();

            $table->unique('source_transfer_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_transfers', function (Blueprint $table) {
            $table->dropUnique(
                'order_transfers_source_transfer_id_unique'
            );

            $table->dropConstrainedForeignId('source_transfer_id');
        });
    }
};