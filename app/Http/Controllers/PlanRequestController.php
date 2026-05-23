<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanRequest;
use App\Models\Member;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Mail;


class PlanRequestController extends Controller
{
    public function request($memberId, Request $request)
    {
        // Validate member and plan existence
        $member = Member::findOrFail($memberId);

        // Create a new plan request
        $planRequest = new PlanRequest();
        $planRequest->member_id = $memberId;
        $planRequest->plan_id = $request->plan_id;
        $planRequest->save();

        return redirect()->route('member.portal')->with('success', 'Membership plan change request submitted successfully.');
    }

    public function update($id, Request $request)
    {
        $planRequest = PlanRequest::findOrFail($id);
        $planRequest->status = $request->status;
        $planRequest->save();
        if ($request->status == 'approved') {
        $planRequest->member->membership_plan_id = $planRequest->plan_id;
        $planRequest->member->membership_start_date = $planRequest->updated_at;
        $planRequest->member->save();
        }

        Mail::to($planRequest->member->email)->queue(new \App\Mail\PlanRequestStatusUpdate($planRequest));

        return redirect()->route('admin.dashboard')->with('success', 'Plan request status updated successfully.');
    }
}
