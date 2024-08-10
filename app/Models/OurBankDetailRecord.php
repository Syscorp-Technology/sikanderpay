<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurBankDetailRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'our_bank_detail_id',
        'type',
        'operation',
        'bank_amount',
        'req_amount',
        'updated_amount',
        'created_by'
    ];
}
