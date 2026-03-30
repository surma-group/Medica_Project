<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {

            // 1. generic_name drop + generic_id add
            $table->dropColumn('generic_name');
            $table->unsignedBigInteger('generic_id')->nullable()->after('name');

            // 2. company_id add (brand_id এর পরে)
            $table->unsignedBigInteger('company_id')->nullable()->after('brand_id');

            // 3. category_id nullable
            $table->unsignedBigInteger('category_id')->nullable()->change();

            // 4. brand_id nullable
            $table->unsignedBigInteger('brand_id')->nullable()->change();

            // 5. barcode nullable
            $table->string('barcode', 50)->nullable()->change();

            // 6. image এর পরে price add
            $table->decimal('price', 10, 2)->default(0)->after('image');

            // Foreign Keys
            $table->foreign('generic_id')->references('id')->on('product_generics')->nullOnDelete();
            $table->foreign('company_id')->references('id')->on('product_companies')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {

            // Drop FKs first
            $table->dropForeign(['generic_id']);
            $table->dropForeign(['company_id']);

            // Remove columns
            $table->dropColumn('generic_id');
            $table->dropColumn('company_id');
            $table->dropColumn('price');

            // Restore generic_name
            $table->string('generic_name')->nullable()->after('name');

            // Revert nullable changes
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->unsignedBigInteger('brand_id')->nullable(false)->change();
            $table->string('barcode', 50)->nullable(false)->change();
        });
    }
};
