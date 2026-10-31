
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('account_number', 12)
                ->nullable()
                ->after('employee_code');

            $table->unique(
                'account_number',
                'employees_account_number_unique'
            );
        });

        // Existing employees-এর জন্য account number তৈরি
        DB::table('employees')
            ->select('id', 'created_at')
            ->orderBy('id')
            ->chunkById(500, function ($employees) {
                foreach ($employees as $employee) {
                    do {
                        $date = $employee->created_at
                            ? date('Ymd', strtotime($employee->created_at))
                            : date('Ymd');

                        $accountNumber = $date . str_pad(
                            (string) random_int(0, 9999),
                            4,
                            '0',
                            STR_PAD_LEFT
                        );
                    } while (
                        DB::table('employees')
                            ->where('account_number', $accountNumber)
                            ->exists()
                    );

                    DB::table('employees')
                        ->where('id', $employee->id)
                        ->update([
                            'account_number' => $accountNumber,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(
                'employees_account_number_unique'
            );

            $table->dropColumn('account_number');
        });
    }
};
