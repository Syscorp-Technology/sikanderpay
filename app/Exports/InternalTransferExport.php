<?php

namespace App\Exports;

use App\Models\InternalTransfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class InternalTransferExport implements FromCollection, WithHeadings
{
    private $internalExpense;

    public function __construct(Collection $internalExpense)
    {
        $this->internalExpense = $internalExpense;
        // dd($internalExpense);
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $serialNumber = 1;
        return $this->internalExpense->map(function ($internalExpense) use (&$serialNumber) {
            return [
                'S.no' => $serialNumber++,
                'ID' => $internalExpense->id,
                'Title' => $internalExpense->title,
                'Note' => $internalExpense->remark,
                // 'Category' => $internalExpense->category->category_name,
                'Date' => $internalExpense->date,
                'Amount' => $internalExpense->amount,
                'Bank From' => $internalExpense->bankFrom->bank_name,
                'Bank To' => $internalExpense->bankTo->bank_name,
                'UTR' => $internalExpense->utr,
                'Banker Status' => $internalExpense->banker_status,
                'Created By' => $internalExpense->createdBy->name,
            ];
        });
    }
    public function headings(): array
    {
        // Customize the headings based on your database columns
        return [
            "S.no",
            'ID',
            'Titile',
            'Note',
            // 'Category',
            'Date',
            'Amount',
            'Bank From',
            'Bank To',
            // 'Payment Mode',
            'UTR',
            // 'Our Bank Detail',
            // 'Financier Status',
            // 'Operation Head Status',
            // 'Superviser Status',
            'Banker Status',
            // 'Status',
            'Created By',
            // 'Created At'
            // Add other headings...
        ];
    }
}
