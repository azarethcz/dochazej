<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('auth.login', compact('employees'));
    }

    /**
     * Employees log in by picking their name, like punching a shared
     * clock-in terminal. If the admin has set a PIN for that employee
     * (see EmployeeController::setPin), it must be entered correctly;
     * employees without a PIN log in with just their name, as before.
     */
    public function loginAsEmployee(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'pin' => ['nullable', 'digits:4'],
        ]);

        $user = User::where('role', 'employee')->findOrFail($data['user_id']);

        if ($user->pin_hash) {
            if (empty($data['pin']) || !Hash::check($data['pin'], $user->pin_hash)) {
                throw ValidationException::withMessages([
                    'pin' => 'Nesprávný PIN.',
                ]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function loginAsAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'role' => 'admin'])) {
            throw ValidationException::withMessages([
                'email' => 'Nesprávný e-mail nebo heslo.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
