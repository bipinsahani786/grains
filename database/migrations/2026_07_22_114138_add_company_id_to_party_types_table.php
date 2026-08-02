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
        Schema::table('party_types', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            $table->dropUnique('party_types_slug_unique');
            $table->unique(['company_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('party_types', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'slug']);
            $table->unique('slug');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
