<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Member;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $memberId = $this->route('member'); // Get the member ID from the route parameter
        $member = Member::find($memberId);
        $userId = $member ? $member->user_id : null; // Get the associated user ID if the member exists
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $memberId . '|unique:users,email,' . $userId,
            'phone_number' => 'required|string|max:20',
            'membership_start_date' => 'required|date',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'status' => 'required|boolean',
        ];
    }
}
