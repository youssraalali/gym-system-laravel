<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Mail\MemberCredentials;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('membershipPlan');

        if ($request->filled('plan')) {

            $query->whereHas('membershipPlan', function ($q) use ($request) {
                $q->where('name', $request->plan);

            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone_number', 'like', '%'.$search.'%');
            });
        }

        $allowed = ['id', 'full_name', 'email', 'phone_number', 'membership_start_date', 'membership_plan_id', 'status'];

        $sort = $request->input('sort', 'id');

        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        if (! in_array($column, $allowed, true)) {
            abort(422, 'Invalid sort column');
        }

        $query->orderBy($column, $direction)->orderBy('id');
        $members = MemberResource::collection($query->paginate(10)->withQueryString());

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

        Mail::to($request->email)->queue(new MemberCredentials($member, $password));

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
