<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::role('Staff')->with('roles')->orderBy('name')->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $user = User::create([
                ...$request->safe()->only(['name', 'email']),
                'password' => Str::password(64),
            ]);
            $user->assignRole(Role::findOrCreate('Staff', 'web'));
        });

        return to_route('admin.staff.index')->with('success', 'Staff account created. Ask the staff member to use Forgot Password to set their password.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        abort_unless($staff->hasRole('Staff') && ! $staff->hasRole('Admin') && ! $staff->isPermanentAdmin(), 403);

        try {
            DB::transaction(fn () => $staff->delete());
        } catch (QueryException $exception) {
            if (! in_array($exception->getCode(), ['23000', '23503'], true)) {
                throw $exception;
            }

            return to_route('admin.staff.index')->withErrors(['staff' => 'This staff account has linked records and cannot be deleted safely.']);
        }

        return to_route('admin.staff.index')->with('success', 'Staff account deleted.');
    }
}
