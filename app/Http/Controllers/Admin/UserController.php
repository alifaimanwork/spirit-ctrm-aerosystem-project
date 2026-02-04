<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function view_user_list(): Response
    {
        $users = User::where('role', '<>', 'superadmin')->get();

        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }

    /**
     * Create one User
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string|max:255|unique:users,staff_id',
            'role' => 'required|string|in:user,admin',
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ]);

        User::create([
            'staff_id' => $request->staff_id,
            'role' => $request->role,
            'name' => $request->name,
            'designation' => $request->designation,
            'email' => $request->email,
        ]);

        return redirect()->route('users')->with('success', 'User created successfully');
    }

    public function update(Request $request)
    {
        $id = $request->input('id');

        // Validation rules
        $validationRules = [
            'staff_id' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($id) {
                $staff_id = User::find($id)->staff_id;
                if ($staff_id !== $value) {
                    if (User::where('staff_id', $value)->exists()) {
                        $fail("The :attribute has already been taken.");
                    }
                };
            }],
            'role' => 'required|string|in:user,admin',
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ];

        // Add password validation if provided
        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($validationRules);

        // Prepare data for update
        $updateData = [
            'staff_id' => $request->input('staff_id'),
            'role' => $request->input('role'),
            'name' => $request->input('name'),
            'designation' => $request->input('designation'),
            'email' => $request->input('email'),
        ];

        // Only add password to update data if it's provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        User::find($id)->update($updateData);

        return redirect()->route('users')->with('success', 'User updated successfully');
    }

    public function delete(Request $request)
    {
        $staff_id = $request->input('staff_id');
        User::destroy($staff_id);
        
        return redirect()->route('users')->with('success', 'User deleted successfully');
    }
}
