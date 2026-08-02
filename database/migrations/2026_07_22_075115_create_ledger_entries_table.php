<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('users')->cascadeOnDelete();
            $table->string('entry_type'); // opening_balance, purchase, sale, payment_in, payment_out, advance, commission_earned, commission_paid, adjustment
            $table->nullableMorphs('reference');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->date('entry_date');
            $table->timestamps();
            
            $table->index(['company_id', 'party_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};