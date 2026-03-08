<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // FK to employees table
            $table->string('method'); // Cash, bKash, etc.
            $table->text('data')->nullable(); // JSON/text field for phone/bank info
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');
            $table->unsignedBigInteger('approve_by')->nullable(); // admin/user who approved
            $table->unsignedBigInteger('created_by'); // who created the request (usually employee)
            $table->timestamps();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approve_by')->references('id')->on('users')->onDelete('set null'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('withdraw_requests');
    }
};
