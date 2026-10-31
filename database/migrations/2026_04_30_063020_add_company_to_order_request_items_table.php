<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_request_items', function (Blueprint $table) {
            $table->string('company')->nullable()->after('strength'); // ✅ add here
        });
    }

    public function down(): void
    {
        Schema::table('order_request_items', function (Blueprint $table) {
            $table->dropColumn('company');
        });
    }
};