<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WithdrawExport implements FromCollection, WithHeadings
{
    private $withdraws;

    public function __construct(Collection $withdraws)
    {
        $this->withdraws = $withdraws;
    }

    public function collection()
    {
        $serialNumber = 1;
        return $this->withdraws->map(function ($withdraws) use (&$serialNumber) {
            return [
                // 'S.no' => $serialNumber++,
                'ID' => $withdraws->id,
                'Date'=>$withdraws->created_at->format('Y-m-d'),
                'User Name'=>$withdraws->platformDetail->platform_username,
                'Bank'=>$withdraws->bank->account_name,
                'Account Number'=>$withdraws->bank->account_number,
                'UTR'=>optional($withdraws->withdrawUtr)->utr ?? '-',
                'Withdraw Amount'=>$withdraws->amount,
                'D Bonus'=>$withdraws->d_chips,
                'Rolling'=>$withdraws->rolling_type,
                'Admin Status'=>$withdraws->admin_status,
                'Admin Status At'=>$withdraws->approvalTimeLine?$withdraws->approvalTimeLine->admin_status_at:'-' ,
                'Banker Status'=>$withdraws->banker_status,
                'Banker Status At'=>$withdraws->approvalTimeLine?$withdraws->approvalTimeLine->banker_status_at:'-' ,
                'CC Status'=>$withdraws->isInformed,
                'CC Status At'=>$withdraws->approvalTimeLine?$withdraws->approvalTimeLine->cc_status_at:'-' ,
                'Created By'=>$withdraws->employee->name,
                "Mobile"=>optional($withdraws->platformDetail->player)->mobile ?? '-',
                "Withdraw Bank"=>optional(optional($withdraws->withdrawUtr)->ourBankDetail)->bank_name ?? '-',
                  // Assuming created by user's name
            ];
        });
    }

    public function headings(): array
    {
        // Customize the headings based on your database columns
        return [
            // 'S.no',
            'ID',
            'Date',
            'User Name',
            'Bank',
            'Account Number',
            'UTR',
            'Withdraw Amount',
            'D Bonus',
            'Rolling',
            'Admin Status',
            'Admin Status At',
            'Banker Status',
            'Banker Status At',
            'CC Status',
            'CC Status At',
            'Created By',
            "Mobile",
            "Withdraw Bank",

            // Add other headings...
        ];
    }
}
