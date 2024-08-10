<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benificiary extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_detail_id',
        'our_bank_details_id',
        'created_by',
        // 'status',
    ];
    public function ourBankDetail()
    {
        return $this->belongsTo(OurBankDetail::class, 'our_bank_details_id');
    }
    public function userBankDetail()
    {
        return $this->belongsTo(bank_detail::class, 'bank_detail_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
