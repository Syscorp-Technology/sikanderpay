<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkTimeline extends Model
{
    use HasFactory;
    protected $fillable=[
        'deposit_id',
        'withdraw_id',
        'type',
        'status',
        'admin_id',
        'banker_id',
        'cc_id',
        'admin_status_at',
        'banker_status_at',
        'cc_status_at',
        'stopped_at',
        'created_by',
        'updated_by'
    ];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function bankerUser()
    {
        return $this->belongsTo(User::class, 'banker_id');
    }
    public function ccUser()
    {
        return $this->belongsTo(User::class, 'cc_id');
    }

    public function deposit()
    {
        return $this->belongsTo(deposit::class);
    }
    public function Withdraw()
    {
        return $this->belongsTo(Withdraw::class,'withdraw_id');
    }
}
