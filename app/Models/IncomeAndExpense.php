<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeAndExpense extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'note',
        'category_id',
        'expense_category_id',
        'date',
        'amount',
        'payment_mode_id',
        'ref_no',
        'type',
        'attachment',
        'our_bank_detail_id',
        'financier_status',
        'operation_head_status',
        'superviser_status',
        'banker_status',
        'status',
        'created_by',
        'updated_by'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }
    public function ourBankDetail()
    {
        return $this->belongsTo(OurBankDetail::class, 'our_bank_detail_id');
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
