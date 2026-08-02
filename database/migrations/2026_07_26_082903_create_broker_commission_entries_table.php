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
        Schema::create('broker_commission_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('broker_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference_type'); // App\Models\Business\Purchase or App\Models\Business\Sale
            $table->unsignedBigInteger('reference_id');
            $table->date('date');
            $table->decimal('quantity', 10, 2);
            $table->decimal('rate', 10, 2); // deal rate
            $table->string('commission_type'); // per_quintal, per_kg, percentage, fixed
            $table->decimal('commission_rate', 10, 2); // the applied rate
            $table->decimal('commission_amount', 12, 2); // the calculated final amount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_commission_entries');
    }
};
