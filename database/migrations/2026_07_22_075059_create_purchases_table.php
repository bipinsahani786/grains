<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('party_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('grain_id')->constrained('grains')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->string('unit');
            $table->decimal('moisture', 5, 2)->nullable();
            $table->decimal('rate', 15, 2);
            $table->decimal('total_unit', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};