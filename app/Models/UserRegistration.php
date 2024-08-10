<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UserRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];


    protected $fillable = [

        'branch_id',
        'name',
        'mobile',
        'dob',
        'lead_source_id',
        'location',
        'date',
        'isActive',
        'r_pay_contact_id',
        'alternative_mobile',
        'auth_user_id',
        'created_by',
        'updated_by',

    ];
    
    public function getMobileAttribute($value)
    {
        
        if ($this->isSuperAdmin()) {
            return $value; // Return the full mobile number for super admins
        }
        
        // Check if the mobile number is at least 4 digits long
        if (strlen($value) >= 4) {
            $lastFourDigits = substr($value, -4);
            $masked = str_repeat('x', strlen($value) - 4) . $lastFourDigits;
            return $masked;
        }
        return $value;
    }
    
    protected function isSuperAdmin()
    {
        $user = Auth::user();
        // Adjust this check based on your application's user role management
        return $user && $user->roles[0]->name === 'Super Admin';
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id'); // Assuming 'lead_source' is the foreign key column
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bankdetails()
    {
        return $this->hasMany(bank_detail::class, 'player_id', 'id');
    }

    public function payment()
    {
        return $this->hasMany(deposit::class, 'user_id');
    }

    public function platformDetails()
    {
        return $this->hasMany(PlatformDetails::class, 'player_id');
    }

}
