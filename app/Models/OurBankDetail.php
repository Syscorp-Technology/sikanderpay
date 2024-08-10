<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OurBankDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'category',
        'bank_name',
        'account_number',
        'amount',
        'ifsc',
        'limit',
        'remarks',
        'count',
        'status',
        'created_by',
        'updated_by',
    ];
    public function withdrawUtr(){
        return $this->hasMany(WithdrawUtr::class,'our_bank_detail','id');
    }
}
