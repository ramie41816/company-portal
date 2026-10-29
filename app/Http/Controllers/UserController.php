<?php

namespace App\Http\Controllers;

use App\Mail\NewUserTemporaryPasswordMail;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $accessLevel = $request->input('access_level');

        $users = User::query()
            ->with(['userInfo', 'roles'])
            ->where('id', '!=', auth()->id()) // Exclude the logged-in user
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhereHas('userInfo', function ($sub) use ($search) {
                            $sub->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('suffix', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn($q, $status) => $q->where('status', $status))
            ->when($accessLevel, function ($q, $level) {
                $q->whereHas('roles', fn($sub) => $sub->where('name', $level));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search', 'status', 'accessLevel'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
            'suffix' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZÀ-ÿ\s\-\.]+$/'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'birthday' => [
                'nullable',
                'date',
                'after_or_equal:1900-01-01',
                'before_or_equal:today',
                function ($attribute, $value, $fail) {
                    if ($value && (date('Y', strtotime($value)) > 9999 || strlen(explode('-', $value)[0]) > 4)) {
                        $fail('The birthday year must not be greater than 9999.');
                    }
                },
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $tempPassword = Str::password(10);

        $user = DB::transaction(function () use ($validated, $tempPassword) {
            $userInfo = UserInfo::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
            ]);

            $newUser = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($tempPassword),
                'access_level' => 'staff', // Fallback/compatibility column if kept in schema
                'status' => $validated['status'],
                'info_id' => $userInfo->id,
            ]);

            // Assign the selected role via pivot table
            $newUser->roles()->sync([$validated['role_id']]);

            return $newUser;
        });

        // Send branded temporary password email
        Mail::to($user->email)->send(new NewUserTemporaryPasswordMail($user, $tempPassword));

        return redirect()->route('users.index')->with('success', 'User account created successfully and temporary password has been emailed.');
    }

    public function show(User $user)
    {
        $user->load(['userInfo', 'roles']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load(['userInfo', 'roles']);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
            'suffix' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZÀ-ÿ\s\-\.]+$/'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'birthday' => [
                'nullable',
                'date',
                'after_or_equal:1900-01-01',
                'before_or_equal:today',
                function ($attribute, $value, $fail) {
                    if ($value && (date('Y', strtotime($value)) > 9999 || strlen(explode('-', $value)[0]) > 4)) {
                        $fail('The birthday year must not be greater than 9999.');
                    }
                },
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            if ($user->userInfo) {
                $user->userInfo->update([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'suffix' => $validated['suffix'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'birthday' => $validated['birthday'] ?? null,
                ]);
            }

            $user->email = $validated['email'];
            $user->status = $validated['status'];
            $user->save();

            // Sync updated role assignment
            $user->roles()->sync([$validated['role_id']]);
        });

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }

    public function sendTemporaryPassword(User $user)
    {
        $tempPassword = Str::password(10);

        $user->update([
            'password' => Hash::make($tempPassword),
        ]);

        // Send branded temporary password email
        Mail::to($user->email)->send(new NewUserTemporaryPasswordMail($user, $tempPassword, true));

        return back()->with('success', 'A new temporary password has been successfully generated and emailed to the user.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User archived successfully.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $accessLevel = $request->input('access_level');

        $users = User::onlyTrashed()
            ->with(['userInfo', 'roles'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhereHas('userInfo', function ($sub) use ($search) {
                            $sub->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('suffix', 'like', "%{$search}%");
                        });
                });
            })
            ->when($accessLevel, function ($q, $level) {
                $q->whereHas('roles', fn($sub) => $sub->where('name', $level));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.archives', compact('users', 'search', 'accessLevel'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.archives')->with('success', 'User restored successfully.');
    }

    public function forceDelete($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::onlyTrashed()->findOrFail($id);
            $userInfo = $user->userInfo;

            $user->forceDelete();

            if ($userInfo) {
                $userInfo->delete();
            }
        });

        return redirect()->route('users.archives')->with('success', 'User permanently deleted.');
    }
}
