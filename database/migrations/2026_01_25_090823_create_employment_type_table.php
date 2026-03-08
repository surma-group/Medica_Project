<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_type', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_type');
    }
};
