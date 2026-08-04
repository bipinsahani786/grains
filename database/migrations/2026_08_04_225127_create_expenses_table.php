<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('expense_no')->nullable();
            $table->unsignedBigInteger('sequence_no')->default(1);
            $table->date('date');
            $table->unsignedBigInteger('category_id');
            $table->text('description')->nullable();
            $table->decimal('amount', 14, 2);
            $table->enum('payment_mode', ['cash', 'bank', 'upi', 'cheque'])->default('cash');
            $table->string('reference_no')->nullable(); // cheque no / UTR
            // Vendor - supports both free text AND existing party
            $table->string('vendor_name')->nullable();
            $table->unsignedBigInteger('vendor_party_id')->nullable();
            // Recurring
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['monthly', 'weekly', 'yearly'])->nullable();
            $table->date('recurring_next_date')->nullable();
            $table->unsignedBigInteger('recurring_parent_id')->nullable(); // links to original
            // Meta
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('restrict');
            $table->foreign('vendor_party_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
