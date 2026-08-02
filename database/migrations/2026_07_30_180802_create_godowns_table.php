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
        Schema::create('godowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('location')->nullable();
            $table->decimal('capacity_in_quintals', 15, 2)->default(0);
            $table->decimal('current_stock_in_quintals', 15, 2)->default(0);
            $table->timestamps();
            
            $table->index(['company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('godowns');
    }
};
