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
            $table->string('purchase_header_path')->nullable();
            $table->string('purchase_footer_path')->nullable();
            $table->string('sale_header_path')->nullable();
            $table->string('sale_footer_path')->nullable();
            $table->text('billing_terms_conditions')->nullable();
            $table->text('billing_bank_details')->nullable();
            $table->string('billing_authorised_signatory_text')->nullable()->default('Authorised Signatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_header_path',
                'purchase_footer_path',
                'sale_header_path',
                'sale_footer_path',
                'billing_terms_conditions',
                'billing_bank_details',
                'billing_authorised_signatory_text',
            ]);
        });
    }
};
