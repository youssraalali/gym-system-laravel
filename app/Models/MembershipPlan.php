<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\MembershipPlanFactory;

class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory;
    protected $fillable =
    [
        'name',
        'duration',
        'price'
    ];
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function planRequests()
    {
        return $this->hasMany(PlanRequest::class, 'plan_id');
    }


}
