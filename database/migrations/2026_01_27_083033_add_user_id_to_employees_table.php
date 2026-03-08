<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Step 1: Add column after district
            $table->unsignedBigInteger('user_id')->nullable()->after('district');

            // Step 2: Add foreign key separately
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);

            // Drop column
            $table->dropColumn('user_id');
        });
    }

};
