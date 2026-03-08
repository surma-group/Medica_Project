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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('company_code')->unique();
            $table->string('company_name');
            $table->string('company_short_name')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('trade_license_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('bin_vat_no')->nullable();
            $table->date('incorporation_date')->nullable();

            // Contact Info
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();

            // Address & Location
            $table->string('logo')->nullable();
            $table->tinyText('address_line_1')->nullable();
            $table->tinyText('address_line_2')->nullable();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();

            // Currency & Timezone (Foreign Keys)
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('timezone_id')->nullable()->constrained('time_zones')->nullOnDelete();


            // Financial Year
            $table->date('financial_year_start')->nullable();
            $table->date('financial_year_end')->nullable();

            // Status & Tracking
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
