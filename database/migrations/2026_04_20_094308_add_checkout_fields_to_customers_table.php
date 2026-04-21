<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            // নাম split (optional)
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');

            // ✅ district relation (nullable)
            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete()
                ->after('address');

            // postcode
            $table->string('postcode')->nullable()->after('district_id');

        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            $table->dropForeign(['district_id']);

            $table->dropColumn([
                'first_name',
                'last_name',
                'district_id',
                'postcode'
            ]);

        });
    }
};