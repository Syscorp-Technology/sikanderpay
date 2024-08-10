<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DepositExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    private $withdraws;

    public function __construct(Collection $withdraws)
    {
        $this->withdraws = $withdraws;
    }

    public function collection()
    {
        return $this->withdraws;
    }

    public function headings(): array
    {
        // Customize the headings based on your database columns
        return [
            'ID',
            'Date',
            'UTRN',
            'User Name',
            'Deposit Bank',
            'Deposit Amount',
            'Bonus',
            'Total Deposit Amount',
            'Admin Status',
            'Banker Status',
            'CC Status',
            'Created By',
            'Mobile'
            // Add other headings...
        ];
    }
    
}
