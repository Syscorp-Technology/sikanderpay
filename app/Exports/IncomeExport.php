<?php

namespace App\Exports;

use App\Models\IncomeAndExpense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IncomeExport implements FromCollection, WithHeadings
{
    private $incomes;

    public function __construct(Collection $incomes)
    {
        $this->incomes = $incomes;
        // dd($incomes);
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $serialNumber = 1;
        return $this->incomes->map(function ($income) use (&$serialNumber) {
            return [
                'S.no' => $serialNumber++,
                'ID' => $income->id,
                'Title' => $income->title,
                'Note' => $income->note,
                'Category' => $income->category->category_name, // Assuming category name
                'Date' => $income->date,
                'Amount' => $income->amount,
                'Payment Mode' => $income->paymentMode->payment_mode_name, // Assuming payment mode name
                'Ref No' => $income->ref_no,
                'Our Bank Detail' => $income->ourBankDetail->bank_name, // Assuming this is a string field
                'Banker Status' => $income->banker_status,
                // 'Status' => $income->status,
                'Created By' => $income->createdBy->name, // Assuming created by user's name
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
            'Category',
            'Date',
            'Amount',
            'Payment Mode',
            'Ref No',
            'Our Bank Detail',
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
