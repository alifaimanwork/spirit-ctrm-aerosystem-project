<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Rules\PasswordNotExist;

class FirstTimeLoginController extends Controller
{
    /**
     * Display the registration view.
     */
    public function view(): Response
    {
        return Inertia::render('Auth/FirstTimeLogin');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'staff_id' => ['required', 'string', 'max:255', 'exists:users,staff_id', new PasswordNotExist],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ]);

        $staffId = $request->staff_id;
        $user = User::where('staff_id', $staffId)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
