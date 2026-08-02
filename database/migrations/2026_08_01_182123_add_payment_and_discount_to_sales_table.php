<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Payment mode: 'regular' = recover later (credit), 'cash_discount' = immediate with % cut
            $table->string('payment_mode')->default('regular')->after('notes'); // 'regular' | 'cash_discount'
            $table->decimal('discount_percent', 5, 2)->default(0)->after('payment_mode'); // % cut on cash discount
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_percent');
            $table->decimal('net_amount', 12, 2)->nullable()->after('discount_amount'); // final amount after discount
            $table->decimal('amount_paid', 12, 2)->default(0)->after('net_amount'); // immediate cash payment
            $table->decimal('outstanding_amount', 12, 2)->default(0)->after('amount_paid');
        });

        // Add name column to sale_charges if missing
        Schema::table('sale_charges', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_charges', 'name')) {
                $table->string('name')->nullable()->after('sale_id');
            }
        });

        // Create sale_payments table
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('mode'); // cash, upi, bank, cheque
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'discount_percent', 'discount_amount', 'net_amount', 'amount_paid', 'outstanding_amount']);
        });
    }
};
