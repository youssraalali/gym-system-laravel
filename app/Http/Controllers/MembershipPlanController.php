<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MembershipPlan;

class MembershipPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = MembershipPlan::all();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
        MembershipPlan::create($request->only(['name', 'duration', 'price']));
        return redirect()->route('plans.index')->with('success', 'Plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
        $plan = MembershipPlan::findOrFail($id);
        $plan->update($request->only(['name', 'duration', 'price']));
        return redirect()->route('plans.show', $plan)->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        if ($plan->members()->count() > 0) {
            return redirect()->route('plans.index')->with('error', 'Cannot delete plan with active members.');
        }
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Plan deleted successfully.');
    }
}
