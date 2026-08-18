<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('billing_bank_details');
            $table->string('account_holder')->nullable()->after('bank_name');
            $table->string('account_no')->nullable()->after('account_holder');
            $table->string('ifsc_code')->nullable()->after('account_no');
            $table->string('branch_name')->nullable()->after('ifsc_code');
            $table->string('upi_id')->nullable()->after('branch_name');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'account_holder',
                'account_no',
                'ifsc_code',
                'branch_name',
                'upi_id'
            ]);
        });
    }
};
