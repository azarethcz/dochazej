<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VacationController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login/employee', [AuthController::class, 'loginAsEmployee'])->name('login.employee')->middleware('guest');
Route::post('/login/admin', [AuthController::class, 'loginAsAdmin'])->name('login.admin')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/clock/in', [DashboardController::class, 'clockIn'])->name('clock.in');
    Route::post('/clock/out', [DashboardController::class, 'clockOut'])->name('clock.out');
    Route::post('/clock/break-start', [DashboardController::class, 'breakStart'])->name('clock.breakStart');
    Route::post('/clock/break-end', [DashboardController::class, 'breakEnd'])->name('clock.breakEnd');

    Route::get('/records', [AttendanceController::class, 'index'])->name('records.index');
    Route::get('/records/export/xlsx', [AttendanceController::class, 'exportXlsx'])->name('records.export.xlsx');
    Route::get('/records/export/pdf', [AttendanceController::class, 'exportPdf'])->name('records.export.pdf');

    Route::get('/vacation', [VacationController::class, 'index'])->name('vacation.index');
    Route::post('/vacation', [VacationController::class, 'store'])->name('vacation.store');

    Route::middleware('admin')->group(function () {
        Route::post('/vacation/{vacation}/approve', [VacationController::class, 'approve'])->name('vacation.approve');
        Route::post('/vacation/{vacation}/reject', [VacationController::class, 'reject'])->name('vacation.reject');

        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::post('/employees/{employee}/pin', [EmployeeController::class, 'setPin'])->name('employees.pin');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    });
});
