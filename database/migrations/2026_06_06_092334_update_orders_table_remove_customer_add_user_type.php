<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 1. drop foreign key first
            $table->dropForeign(['customer_id']);

            // 2. drop customer_id column
            $table->dropColumn('customer_id');

            // 3. add user_type_id
            $table->unsignedBigInteger('user_type_id')->after('id');

            // 4. add reference_id (customer/partner/agent id)
            $table->unsignedBigInteger('reference_id')->after('user_type_id');

            // 5. foreign key to user_type table
            $table->foreign('user_type_id')
                ->references('id')
                ->on('user_type')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropForeign(['user_type_id']);

            $table->dropColumn(['user_type_id', 'reference_id']);

            $table->unsignedBigInteger('customer_id')->after('id');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }
};