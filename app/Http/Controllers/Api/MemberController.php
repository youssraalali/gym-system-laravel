<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use App\Models\User;
use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = MemberResource::collection(Member::with('membershipPlan')->get());
        return $members;
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(StoreMemberRequest $request)
        {
            $password = Str::random(12);
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);


            $member = Member::create(array_merge($request->only(['full_name', 'email', 'phone_number', 'membership_start_date', 'membership_plan_id']), ['user_id' => $user->id]));

            Mail::to($request->email)->queue(new \App\Mail\MemberCredentials($member, $password));

            return response()->json(['message' => 'Member created successfully', 'member' => new MemberResource($member->load('membershipPlan'))], 201);
        }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $member = Member::with('membershipPlan')->findOrFail($id);
        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, string $id)
    {
        $member = Member::findOrFail($id);

            if ($user = $member->user) {
            $user->update([
                'name' => $request->full_name,
                'email' => $request->email,
            ]);
        }

        $member->update($request->only(['full_name', 'email', 'phone_number', 'membership_start_date', 'membership_plan_id', 'status']));

        return response()->json(['message' => 'Member updated successfully', 'member' => new MemberResource($member->load('membershipPlan'))], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();
        return response()->json(['message' => 'Member deleted successfully'], 200);
    }
}
