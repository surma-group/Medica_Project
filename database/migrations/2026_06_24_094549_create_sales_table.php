<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_type_id')
                ->constrained('user_type')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('sale_number')->unique();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            $table->tinyInteger('payment_method')
                ->default(1)
                ->comment('1=cash,2=bank,3=mobile_banking');

            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=pending,1=completed,2=processing,3=cancelled');

            $table->text('note')->nullable();
            // কে sale করেছে
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
