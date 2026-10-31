<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('add_money_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('payment_account_id')
                ->constrained()
                ->onDelete('cascade');

            $table->decimal('amount', 10, 2);

            $table->string('transaction_id');
            $table->text('note')->nullable();

            // 0 = pending, 1 = approved, 2 = rejected
            $table->tinyInteger('status')->default(0);

            // approval info
            $table->text('approve_note')->nullable();
            $table->foreignId('approve_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approve_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('add_money_requests');
    }
};