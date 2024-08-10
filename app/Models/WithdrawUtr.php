<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawUtr extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdraw_id', 'utr', 'our_bank_detail','payout_id'
    ];

    public function withdraw()
    {
        return $this->belongsTo(Withdraw::class, 'withdraw_id');
    }
    public function ourBankDetail()
    {
        return $this->belongsTo(OurBankDetail::class, 'our_bank_detail');
    }
}
