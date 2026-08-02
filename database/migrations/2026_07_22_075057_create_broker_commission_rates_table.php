<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broker_commission_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('broker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('grain_id')->constrained('grains')->cascadeOnDelete();
            $table->string('commission_type'); // per_unit, percentage
            $table->decimal('rate', 10, 2);
            $table->string('applies_to'); // purchase, sale, both
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_commission_rates');
    }
};