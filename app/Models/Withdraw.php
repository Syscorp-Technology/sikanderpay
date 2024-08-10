<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Withdraw extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];


    protected $fillable = [

        'platform_detail_id',
        'bank_name_id',
        'amount',
        'admin_status',
        'is_bonus_eligible',
        'image',
        'banker_status',
        'rolling_type',
        'status',
        'remark',
        'isInformed',
        'created_by',
        'updated_by',
        'd_chips',
        'assigned_to',
        'fees',
        'tax'
    ];

    public function bank()
    {
        return $this->belongsTo(bank_detail::class, 'bank_name_id');
    }
    public function platForm()
    {
        return $this->belongsTo(PlatForm::class, 'platform_id');
    }

    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function platformDetail()
    {
        return $this->belongsTo(PlatformDetails::class, 'platform_detail_id');
    }
    public function withdrawUtr()
    {
        return $this->hasOne(WithdrawUtr::class, 'withdraw_id');
    }
    public function approvalTimeLine()
    {
        return $this->hasOne(ApprovalWorkTimeline::class, 'withdraw_id');
    }
}
