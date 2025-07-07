<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Employee;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Dynamic dashboard routing based on user role
Route::get('dashboard', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user && $user->isEmployee()) {
        return redirect()->route('employee.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Employee Management
    Route::resource('employees', Admin\EmployeeController::class);

    // Face Enrollment Management
    Route::prefix('face-enrollment')->name('face-enrollment.')->group(function () {
        Route::get('{employee}', [Admin\FaceEnrollmentController::class, 'show'])->name('show');
        Route::post('{employee}', [Admin\FaceEnrollmentController::class, 'store'])->name('store');
        Route::get('{employee}/status', [Admin\FaceEnrollmentController::class, 'status'])->name('status');
        Route::delete('{employee}', [Admin\FaceEnrollmentController::class, 'destroy'])->name('destroy');
    });

    // Location Management
    Route::resource('locations', Admin\LocationController::class);
    Route::post('locations/validate', [Admin\LocationController::class, 'validateLocation'])
        ->name('locations.validate');


    // Attendance Management
    Route::resource('attendances', Admin\AttendanceController::class);
    Route::get('attendances-monitor', [Admin\AttendanceController::class, 'monitor'])
        ->name('attendances.monitor');
    Route::post('attendances/bulk-update', [Admin\AttendanceController::class, 'bulkUpdate'])
        ->name('attendances.bulk-update');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [Admin\ReportController::class, 'index'])->name('index');
        Route::get('attendance', [Admin\ReportController::class, 'attendance'])->name('attendance');
        Route::get('summary', [Admin\ReportController::class, 'summary'])->name('summary');
        Route::get('analytics', [Admin\ReportController::class, 'analytics'])->name('analytics');
    });
});

// Employee Routes
Route::middleware(['auth', 'employee', 'ensure.employee.profile'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('dashboard', [Employee\DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [Employee\DashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [Employee\DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('attendance-stats', [Employee\DashboardController::class, 'attendanceStats'])->name('attendance.stats');

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [Employee\AttendanceController::class, 'index'])->name('index');
        Route::post('record', [Employee\AttendanceController::class, 'record'])
            ->middleware('rate.limit.attendance')->name('record');
        Route::get('history', [Employee\AttendanceController::class, 'history'])->name('history');
        Route::get('{attendance}', [Employee\AttendanceController::class, 'show'])->name('show');
    });

    // Face Enrollment
    Route::prefix('face-enrollment')->name('face-enrollment.')->group(function () {
        Route::get('/', [Employee\FaceEnrollmentController::class, 'index'])->name('index');
        Route::post('/', [Employee\FaceEnrollmentController::class, 'store'])->name('store');
        Route::get('status', [Employee\FaceEnrollmentController::class, 'status'])->name('status');
        Route::delete('/', [Employee\FaceEnrollmentController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
});

require __DIR__ . '/auth.php';
