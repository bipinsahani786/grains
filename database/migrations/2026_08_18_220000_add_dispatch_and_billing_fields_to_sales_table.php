<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('po_no')->nullable()->after('notes');
            $table->integer('bags_count')->nullable()->after('po_no');
            $table->string('truck_no')->nullable()->after('bags_count');
            $table->string('driver_name')->nullable()->after('truck_no');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->decimal('truck_fare', 15, 2)->nullable()->after('driver_phone');
            $table->decimal('freight_advance', 15, 2)->nullable()->after('truck_fare');
            $table->decimal('freight_balance', 15, 2)->nullable()->after('freight_advance');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'po_no',
                'bags_count',
                'truck_no',
                'driver_name',
                'driver_phone',
                'truck_fare',
                'freight_advance',
                'freight_balance'
            ]);
        });
    }
};
