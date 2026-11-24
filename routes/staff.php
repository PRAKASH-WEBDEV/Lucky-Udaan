<?php

use App\Http\Controllers\Staff\PortalController;
use Illuminate\Support\Facades\Route;

/*
| Staff portal — loaded with the "staff." name prefix and "/staff" URL prefix (see bootstrap/app.php).
| Employees sign in with the email + password set on their Employee record. They can only:
| mark attendance, see office timings, read notices and apply for leave.
*/

Route::middleware('guest:employee')->group(function () {
    Route::get('login', [PortalController::class, 'showLogin'])->name('login');
    Route::post('login', [PortalController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth:employee')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::post('check-in', [PortalController::class, 'checkIn'])->name('check-in');
    Route::post('check-out', [PortalController::class, 'checkOut'])->name('check-out');
    Route::get('leave', [PortalController::class, 'leave'])->name('leave');
    Route::post('leave', [PortalController::class, 'applyLeave'])->name('leave.apply');
    Route::delete('leave/{id}', [PortalController::class, 'cancelLeave'])->whereNumber('id')->name('leave.cancel');
    Route::get('notices', [PortalController::class, 'notices'])->name('notices');
    Route::post('logout', [PortalController::class, 'logout'])->name('logout');
});
