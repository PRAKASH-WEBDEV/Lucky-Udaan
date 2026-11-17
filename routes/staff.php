<?php

use App\Http\Controllers\Staff\PortalController;
use Illuminate\Support\Facades\Route;

/*
| Staff portal — loaded with the "staff." name prefix and "/staff" URL prefix (see bootstrap/app.php).
| Employees sign in with the email + password set on their Employee record and mark their own attendance.
*/

Route::middleware('guest:employee')->group(function () {
    Route::get('login', [PortalController::class, 'showLogin'])->name('login');
    Route::post('login', [PortalController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth:employee')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::post('check-in', [PortalController::class, 'checkIn'])->name('check-in');
    Route::post('check-out', [PortalController::class, 'checkOut'])->name('check-out');
    Route::post('logout', [PortalController::class, 'logout'])->name('logout');
});
