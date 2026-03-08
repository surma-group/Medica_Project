<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employment_type')->constrained('employment_type')->cascadeOnDelete();
            $table->foreignId('district')->constrained('districts')->cascadeOnDelete();

            $table->string('employee_code')->unique();

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('full_name');

            $table->tinyInteger('gender')->comment('1=Male, 2=Female');

            $table->date('date_of_birth')->nullable();
            $table->date('joining_date')->nullable();

            $table->string('personal_email')->nullable();
            $table->string('official_email')->nullable();

            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();

            $table->tinyText('permanent_address')->nullable();
            $table->tinyText('present_address')->nullable();

            $table->string('photo')->nullable();
            $table->string('nid_no')->nullable();
            $table->string('passport_no')->nullable();

            $table->string('joining_letter')->nullable();
            $table->string('resume')->nullable();
            $table->string('other_documents')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
