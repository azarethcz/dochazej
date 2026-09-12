<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'pin' => ['nullable', 'digits:4'],
        ]);

        User::create([
            'name' => $data['name'],
            'position' => $data['position'] ?? null,
            'role' => 'employee',
            'pin_hash' => !empty($data['pin']) ? Hash::make($data['pin']) : null,
        ]);

        return back()->with('status', 'Zaměstnanec byl přidán.');
    }

    public function destroy(User $employee)
    {
        abort_if($employee->role !== 'employee', 404);
        $employee->delete();

        return back()->with('status', 'Zaměstnanec byl odebrán. Historické záznamy zůstávají v databázi.');
    }

    /**
     * Set or clear an employee's login PIN. Submitting an empty value
     * removes the PIN, returning that employee to name-only login.
     */
    public function setPin(Request $request, User $employee)
    {
        abort_if($employee->role !== 'employee', 404);

        $data = $request->validate([
            'pin' => ['nullable', 'digits:4'],
        ]);

        $employee->update([
            'pin_hash' => !empty($data['pin']) ? Hash::make($data['pin']) : null,
        ]);

        return back()->with('status', !empty($data['pin'])
            ? "PIN pro {$employee->name} byl nastaven."
            : "PIN pro {$employee->name} byl odebrán.");
    }
}
