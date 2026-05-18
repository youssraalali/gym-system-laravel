<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'membership_start_date',
        'status',
        'membership_plan_id',
        'user_id'
    ];
    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
