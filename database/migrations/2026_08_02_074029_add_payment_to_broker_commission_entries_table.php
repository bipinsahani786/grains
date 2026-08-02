<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broker_commission_entries', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('commission_amount'); // pending | paid
            $table->decimal('amount_paid', 12, 2)->default(0)->after('payment_status');
            $table->date('paid_at')->nullable()->after('amount_paid');
            $table->string('paid_mode')->nullable()->after('paid_at'); // Cash | Cheque | Online | NEFT
            $table->text('payment_notes')->nullable()->after('paid_mode');
        });
    }

    public function down(): void
    {
        Schema::table('broker_commission_entries', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'amount_paid', 'paid_at', 'paid_mode', 'payment_notes']);
        });
    }
};
