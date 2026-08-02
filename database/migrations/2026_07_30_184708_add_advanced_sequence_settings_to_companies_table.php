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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('purchase_year_format')->nullable()->after('purchase_prefix'); // None, YYYY, YY-YY, YYYY-YYYY
            $table->integer('purchase_sequence_length')->default(4)->after('purchase_year_format');
            $table->integer('purchase_sequence_start')->default(1)->after('purchase_sequence_length');
            
            $table->string('sale_year_format')->nullable()->after('sale_prefix');
            $table->integer('sale_sequence_length')->default(4)->after('sale_year_format');
            $table->integer('sale_sequence_start')->default(1)->after('sale_sequence_length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_year_format', 'purchase_sequence_length', 'purchase_sequence_start',
                'sale_year_format', 'sale_sequence_length', 'sale_sequence_start'
            ]);
        });
    }
};
