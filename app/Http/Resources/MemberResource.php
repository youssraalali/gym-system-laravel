<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'membership_plan_id' => $this->membership_plan_id,
            'membership_name' => $this->membershipPlan->name ?? null,
            'membership_start_date' => $this->membership_start_date,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'user_id' => $this->user_id,
        ];
    }
}
