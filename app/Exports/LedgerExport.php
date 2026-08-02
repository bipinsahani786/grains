<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LedgerExport implements FromCollection, WithHeadings, WithMapping
{
    protected $entries;

    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    public function collection()
    {
        return $this->entries;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Party',
            'Type',
            'Debit (₹)',
            'Credit (₹)',
            'Balance (₹)',
            'Details',
        ];
    }

    public function map($entry): array
    {
        $details = '';
        if ($entry->entry_type == 'purchase' && $entry->reference) {
            $parts = [];
            foreach ($entry->reference->items ?? [] as $item) {
                $parts[] = ($item->grain->name ?? 'N/A') . ' (' . $item->quantity . ' ' . $item->unit . ' @ ' . $item->rate . ')';
            }
            $details = implode(', ', $parts);
        } elseif ($entry->entry_type == 'sale' && $entry->reference) {
            $details = ($entry->reference->grain->name ?? 'N/A') . ' (' . $entry->reference->quantity . ' ' . $entry->reference->unit . ' @ ' . $entry->reference->rate . ')';
        }

        return [
            \Carbon\Carbon::parse($entry->entry_date)->format('d M, Y'),
            $entry->party->name ?? 'N/A',
            ucfirst($entry->entry_type),
            $entry->debit > 0 ? $entry->debit : '',
            $entry->credit > 0 ? $entry->credit : '',
            $entry->balance_after,
            $details,
        ];
    }
}
