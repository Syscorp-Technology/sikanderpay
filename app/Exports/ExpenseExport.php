<?php

namespace App\Exports;

use App\Models\IncomeAndExpense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseExport implements FromCollection, WithHeadings
{
    private $expense;

    public function __construct(Collection $expense)
    {
        $this->expense = $expense;
        // dd($incomes);
    }
    public function collection()
    {
        $serialNumber = 1;
        return $this->expense->map(function ($expense) use (&$serialNumber) {
            return [
                'S.no' => $serialNumber++,
                'ID' => $expense->id,
                'Title' => $expense->title,
                'Note' => $expense->note,
                'Category' => $expense->expenseCategory->expense_category_name, // Assuming category name
                'Date' => $expense->date,
                'Amount' => $expense->amount,
                'Payment Mode' => $expense->paymentMode->payment_mode_name, // Assuming payment mode name
                'Ref No' => $expense->ref_no,
                'Our Bank Detail' => $expense->ourBankDetail?$expense->ourBankDetail->bank_name:'-', // Assuming this is a string field
                'Banker Status' => $expense->banker_status,
                // 'Status' => $expense->status,
                'Created By' => $expense->createdBy->name, // Assuming created by user's name
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
