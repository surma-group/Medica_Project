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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->tinyText('description')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');

            // Common Laravel fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps(); // created_at + updated_at
            $table->softDeletes(); // deleted_at for soft delete

            // Foreign key for user who created
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
