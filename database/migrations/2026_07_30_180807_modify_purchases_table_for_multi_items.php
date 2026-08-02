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
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['grain_id']);
            $table->dropColumn(['grain_id', 'quantity', 'unit', 'moisture', 'rate', 'total_unit']);
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('grain_id')->nullable()->constrained('grains')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('moisture', 5, 2)->nullable();
            $table->decimal('rate', 15, 2)->nullable();
            $table->decimal('total_unit', 15, 2)->nullable();
        });
    }
};
