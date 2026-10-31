<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_transfers', function (Blueprint $table) {
            $table->id();

            $table->string('transfer_number', 50)->unique();

            $table->unsignedBigInteger('order_id');

            $table->unsignedTinyInteger('from_type');
            $table->unsignedBigInteger('from_id')->nullable();

            $table->unsignedTinyInteger('to_type');
            $table->unsignedBigInteger('to_id');

            $table->unsignedBigInteger('assigned_employee_id')->nullable();

            $table->unsignedTinyInteger('status')->default(0);

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->restrictOnDelete();

            $table->foreign('assigned_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->index(['from_type', 'from_id']);
            $table->index(['to_type', 'to_id']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_transfers');
    }
};
