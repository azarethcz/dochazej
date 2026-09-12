<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employees = $user->isAdmin()
            ? User::where('role', 'employee')->orderBy('name')->get()
            : collect([$user]);

        $employeeId = $request->input('employee_id', $employees->first()?->id);
        $month = $request->input('month', now()->format('Y-m'));

        $employee = $employees->firstWhere('id', (int) $employeeId) ?? $employees->first();
        $rows = $employee ? $this->buildMonthRows($employee, $month) : collect();

        return view('attendance.index', [
            'employees' => $employees,
            'selectedEmployeeId' => $employee?->id,
            'month' => $month,
            'rows' => $rows,
            'monthTotalMinutes' => $rows->sum('workedMinutes'),
            'monthBreakMinutes' => $rows->sum('breakMinutes'),
        ]);
    }

    /**
     * Build one row per calendar day in the given month (Y-m) for an employee,
     * with their attendance sessions and computed totals.
     */
    private function buildMonthRows(User $employee, string $month)
    {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('clock_in', [$start, $end])
            ->with('breakPeriods')
            ->orderBy('clock_in')
            ->get()
            ->groupBy(fn ($a) => $a->clock_in->format('Y-m-d'));

        $rows = collect();
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $dayAttendances = $attendances->get($day->format('Y-m-d'), collect());
            $workedMinutes = $dayAttendances->sum(fn ($a) => $a->workedMinutes() ?? 0);
            $breakMinutes = $dayAttendances->sum(fn ($a) => $a->breakMinutes());

            $rows->push([
                'date' => $day->copy(),
                'attendances' => $dayAttendances,
                'workedMinutes' => $workedMinutes,
                'breakMinutes' => $breakMinutes,
            ]);
        }

        return $rows;
    }

    /**
     * Requires: composer require maatwebsite/excel
     */
    public function exportXlsx(Request $request)
    {
        $user = $request->user();
        $employees = $user->isAdmin() ? User::where('role', 'employee')->get() : collect([$user]);
        $employee = $employees->firstWhere('id', (int) $request->input('employee_id')) ?? $employees->first();
        $month = $request->input('month', now()->format('Y-m'));

        $rows = $this->buildMonthRows($employee, $month);

        return Excel::download(new AttendanceExport($employee, $rows), "dochazka_{$employee->name}_{$month}.xlsx");
    }

    /**
     * Generates a real PDF file server-side via barryvdh/laravel-dompdf
     * (composer require barryvdh/laravel-dompdf).
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $employees = $user->isAdmin() ? User::where('role', 'employee')->get() : collect([$user]);
        $employee = $employees->firstWhere('id', (int) $request->input('employee_id')) ?? $employees->first();
        $month = $request->input('month', now()->format('Y-m'));
        $rows = $this->buildMonthRows($employee, $month);

        $pdf = Pdf::loadView('attendance.print', [
            'employee' => $employee,
            'month' => $month,
            'rows' => $rows,
            'monthTotalMinutes' => $rows->sum('workedMinutes'),
            'monthBreakMinutes' => $rows->sum('breakMinutes'),
        ]);

        return $pdf->download("dochazka_{$employee->name}_{$month}.pdf");
    }
}
