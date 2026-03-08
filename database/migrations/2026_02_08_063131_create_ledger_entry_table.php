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
        Schema::create('ledger_entry', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('voucher_id')
                ->constrained('voucher_entry')
                ->cascadeOnDelete();
            $table->foreignId('ledger_id')
                ->constrained('ledger')
                ->cascadeOnDelete();
            $table->decimal('debit', 15, 4)->default(0);
            $table->decimal('credit', 15, 4)->default(0);
            $table->string('note')->nullable();
            $table->timestamp('time')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entry');
    }
};
