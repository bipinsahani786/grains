<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('lot_no');
            $table->foreignId('grain_id')->constrained('grains')->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->decimal('initial_quantity', 15, 2);
            $table->decimal('remaining_quantity', 15, 2);
            $table->decimal('moisture', 5, 2)->nullable();
            $table->decimal('rate', 15, 2);
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
            
            $table->index(['company_id', 'grain_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};