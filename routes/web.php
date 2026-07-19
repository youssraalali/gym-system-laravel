<?php

use App\Http\Controllers\ProfileController;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\PlanRequestController;
use App\Models\PlanRequest;
use App\Http\Resources\UserCollection;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users', function () {
    return UserCollection::collection(User::all()->keyBy->id);
});

Route::get('/user/{id}', function (string $id) {
    return new UserCollection([User::findOrFail($id)]);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::resource('members', MemberController::class);
    Route::resource('plans', MembershipPlanController::class);
    Route::get('/admin/dashboard', [MemberController::class, 'dashboard'])->name('admin.dashboard');
    Route::put('/plan-requests/{id}', [App\Http\Controllers\PlanRequestController::class, 'update'])->name('plan-requests.update');
});

Route::middleware(['auth', 'role:member'])->group(function () {
        Route::get('/member/portal', [MemberPortalController::class, 'index'])->name('member.portal');
        Route::post('/member/request-plan/{memberId}', [App\Http\Controllers\PlanRequestController::class, 'request'])->name('member.request-plan');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/gym', function () {
    return view('gym');
});

Route::get('/test', function(){
    return new App\Mail\PlanRequestStatusUpdate(PlanRequest::with('member')->first());
});

require __DIR__.'/auth.php';
