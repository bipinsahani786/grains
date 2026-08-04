<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRecurringExpenses extends Command
{
    protected $signature = 'expenses:process-recurring';
    protected $description = 'Generate new expense records for due recurring expenses';

    public function handle()
    {
        $today = Carbon::today();
        $this->info("Processing recurring expenses for " . $today->format('Y-m-d'));

        // Find all expenses marked as recurring where the next date has arrived or passed
        $dueExpenses = Expense::where('is_recurring', true)
            ->whereNotNull('recurring_next_date')
            ->whereDate('recurring_next_date', '<=', $today)
            ->get();

        if ($dueExpenses->isEmpty()) {
            $this->info("No recurring expenses due.");
            return;
        }

        $count = 0;

        foreach ($dueExpenses as $parentExpense) {
            DB::transaction(function () use ($parentExpense, &$count) {
                // Determine new sequence number for the company
                $lastSeq = Expense::where('company_id', $parentExpense->company_id)->max('sequence_no') ?? 0;
                $nextSeq = $lastSeq + 1;
                $expenseNo = 'EXP-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

                // 1. Create the new child expense instance
                $child = $parentExpense->replicate([
                    'is_recurring', 'recurring_frequency', 'recurring_next_date', 'recurring_parent_id', 'created_at', 'updated_at'
                ]);

                $child->expense_no = $expenseNo;
                $child->sequence_no = $nextSeq;
                $child->date = $parentExpense->recurring_next_date; // The date it was actually due
                $child->is_recurring = false;
                $child->recurring_parent_id = $parentExpense->id;
                
                // Add a small note
                $notePrefix = "(Auto-generated recurring instance) ";
                $child->notes = $notePrefix . $child->notes;
                
                $child->save();

                // 2. Update the parent's next due date
                $nextDate = Carbon::parse($parentExpense->recurring_next_date);
                if ($parentExpense->recurring_frequency === 'monthly') {
                    $nextDate->addMonth();
                } elseif ($parentExpense->recurring_frequency === 'weekly') {
                    $nextDate->addWeek();
                } elseif ($parentExpense->recurring_frequency === 'yearly') {
                    $nextDate->addYear();
                } else {
                    // Fallback to monthly if somehow null but marked recurring
                    $nextDate->addMonth();
                }

                $parentExpense->recurring_next_date = $nextDate;
                $parentExpense->save();

                $count++;
            });
        }

        $this->info("Successfully processed {$count} recurring expenses.");
        Log::info("ProcessRecurringExpenses generated {$count} new expenses.");
    }
}
