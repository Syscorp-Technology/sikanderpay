<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'date',
        'bank_from',
        'bank_to',
        'amount',
        'superviser_status',
        'banker_status',
        'utr',
        'attachment',
        'remark',
        'status',
        'created_by',
        'updated_by'
    ];
    public function bankFrom()
    {
        return $this->belongsTo(OurBankDetail::class, 'bank_from');
    }
    public function bankTo()
    {
        return $this->belongsTo(OurBankDetail::class, 'bank_to');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
