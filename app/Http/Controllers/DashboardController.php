<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employees = $user->isAdmin()
            ? User::where('role', 'employee')->orderBy('name')->get()
            : User::whereKey($user->id)->get();

        // Preload today's attendance (with breaks) for whichever employees are visible.
        $employees->load(['attendances' => function ($query) {
            $query->whereDate('clock_in', today())->with('breakPeriods');
        }]);

        return view('dashboard', [
            'employees' => $employees,
            'clockedIn' => $employees->filter(fn ($e) => $e->attendances->firstWhere('clock_out', null))->count(),
        ]);
    }

    private function targetUser(Request $request): User
    {
        $user = $request->user();

        if ($user->isAdmin() && $request->filled('user_id')) {
            return User::where('role', 'employee')->findOrFail($request->input('user_id'));
        }

        return $user;
    }

    public function clockIn(Request $request)
    {
        $target = $this->targetUser($request);

        if (!$target->openAttendance()) {
            Attendance::create(['user_id' => $target->id, 'clock_in' => now()]);
        }

        return back();
    }

    public function clockOut(Request $request)
    {
        $target = $this->targetUser($request);
        $attendance = $target->openAttendance();

        if ($attendance) {
            if ($openBreak = $attendance->openBreak()) {
                $openBreak->update(['end' => now()]);
            }
            $attendance->update(['clock_out' => now()]);
        }

        return back();
    }

    public function breakStart(Request $request)
    {
        $target = $this->targetUser($request);
        $attendance = $target->openAttendance();

        if ($attendance && !$attendance->openBreak()) {
            $attendance->breakPeriods()->create(['start' => now()]);
        }

        return back();
    }

    public function breakEnd(Request $request)
    {
        $target = $this->targetUser($request);
        $attendance = $target->openAttendance();

        if ($attendance && $openBreak = $attendance->openBreak()) {
            $openBreak->update(['end' => now()]);
        }

        return back();
    }
}
