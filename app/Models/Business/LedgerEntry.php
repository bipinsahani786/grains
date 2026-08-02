<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class LedgerEntry extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
    protected $fillable = [
        'company_id', 'party_id', 'entry_type', 'reference_type', 'reference_id',
        'debit', 'credit', 'balance_after', 'entry_date'
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function party()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'party_id');
    }

    /**
     * Recalculates the balance_after for all ledger entries of a specific party.
     * This is required when an entry from the past is deleted or modified.
     */
    public static function recalculateForParty($companyId, $partyId)
    {
        $party = \App\Models\Core\User::find($partyId);
        if (!$party) return;

        $openingBalance = $party->opening_balance ?? 0;
        
        // Ensure proper sign based on opening_balance_type (credit is -, debit is +)
        if ($party->opening_balance_type === 'credit') {
            $openingBalance = -abs($openingBalance);
        } elseif ($party->opening_balance_type === 'debit') {
            $openingBalance = abs($openingBalance);
        }

        $entries = self::where('company_id', $companyId)
            ->where('party_id', $partyId)
            ->orderBy('entry_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $currentBalance = $openingBalance;

        foreach ($entries as $entry) {
            $currentBalance = $currentBalance + $entry->debit - $entry->credit;
            $entry->balance_after = $currentBalance;
            $entry->save();
        }
    }
}
