<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::all();
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = MembershipPlan::all();
        return view('members.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
        {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:members|unique:users,email',
                'phone_number' => 'required|string|max:20',
                'membership_start_date' => 'required|date',
                'membership_plan_id' => 'required|exists:membership_plans,id',
            ]);

            $password = Str::random(12);
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);


            $member = Member::create(array_merge($request->only(['full_name', 'email', 'phone_number', 'membership_start_date', 'membership_plan_id']), ['user_id' => $user->id]));

            Mail::to($request->email)->queue(new \App\Mail\MemberCredentials($member, $password));


            return redirect()->route('members.index')->with('success', 'Member created successfully.');
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::findOrFail($id);
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = Member::findOrFail($id);
        $plans = MembershipPlan::all();
        return view('members.edit', compact('member', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:members,email,' . $member->id,
            'phone_number' => 'required|string|max:20',
            'membership_start_date' => 'required|date',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'status' => 'required|boolean',
        ]);

            if ($user = $member->user) {
            $user->update([
                'name' => $request->full_name,
                'email' => $request->email,
            ]);
        }

        $member->update($request->only(['full_name', 'email', 'phone_number', 'membership_start_date', 'membership_plan_id', 'status']));

        return redirect()->route('members.show', $member)->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
