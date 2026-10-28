<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $accessLevel = $request->input('access_level');

        $query = User::with('userInfo')->latest();

        // Search filter across user email and user_infos name fields
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('userInfo', function ($sub) use ($search) {
                        $sub->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('suffix', 'like', "%{$search}%");
                    });
            });
        }

        // Active / Inactive status filter
        if (! empty($status)) {
            $query->where('status', $status);
        }

        // RBAC Access Level filter
        if (! empty($accessLevel)) {
            $query->where('access_level', $accessLevel);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users', 'search', 'status', 'accessLevel'));
    }

    public function show(User $user)
    {
        $user->load('userInfo');

        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
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
                    // Check if the year has more than 4 digits or exceeds 9999
                    if ($value && (date('Y', strtotime($value)) > 9999 || strlen(explode('-', $value)[0]) > 4)) {
                        $fail('The birthday year must not be greater than 9999.');
                    }
                },
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'access_level' => ['required', 'string', 'in:admin,manager,staff'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $userInfo = UserInfo::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
            ]);

            return User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'access_level' => $validated['access_level'],
                'status' => $validated['status'],
                'info_id' => $userInfo->id,
            ]);
        });

        // Safely redirect to the exact newly created user's show page
        return redirect()->route('users.show', $user)->with('success', 'User account created successfully.');
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $userInfo = $user->userInfo;
            $user->delete();
            if ($userInfo) {
                $userInfo->delete();
            }
        });

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $accessLevel = $request->input('access_level');

        $query = User::with('userInfo')->where('status', 'inactive')->latest();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('userInfo', function ($sub) use ($search) {
                        $sub->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('suffix', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($accessLevel)) {
            $query->where('access_level', $accessLevel);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.archives', compact('users', 'search', 'accessLevel'));
    }

    public function edit(User $user)
    {
        $user->load('userInfo');

        return view('users.edit', compact('user'));
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
                    // Check if the year has more than 4 digits or exceeds 9999
                    if ($value && (date('Y', strtotime($value)) > 9999 || strlen(explode('-', $value)[0]) > 4)) {
                        $fail('The birthday year must not be greater than 9999.');
                    }
                },
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'access_level' => ['required', 'string', 'in:admin,manager,staff'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            // Update or create user info
            if ($user->userInfo) {
                $user->userInfo->update([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'suffix' => $validated['suffix'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'birthday' => $validated['birthday'] ?? null,
                ]);
            } else {
                $userInfo = UserInfo::create([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'suffix' => $validated['suffix'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'birthday' => $validated['birthday'] ?? null,
                ]);
                $user->info_id = $userInfo->id;
            }

            $user->email = $validated['email'];
            $user->access_level = $validated['access_level'];
            $user->status = $validated['status'];

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
        });

        // Redirect to show page
        return redirect()->route('users.show', ['user' => $user])->with('success', 'User account updated successfully.');
    }
}
