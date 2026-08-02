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
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('sequence_no')->nullable()->after('company_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('sequence_no')->nullable()->after('company_id');
            $table->string('sale_no')->nullable()->after('sequence_no'); // Adding sale_no as well
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('sequence_no');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['sequence_no', 'sale_no']);
        });
    }
};
