<?php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\FeeController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\IDCardController;
use App\Http\Controllers\API\AdminReportController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // 
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    Route::get('/seats', [StudentController::class, 'seatStatus']);

    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/monthly', [ExpenseController::class, 'monthlyExpense']);
    Route::get('/reports/profit', [ExpenseController::class, 'profitReport']);

    Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn']);
    Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut']);
    Route::get('/attendance/my-report', [AttendanceController::class, 'myMonthlyReport']);
    Route::get('/id-card', [IDCardController::class, 'generate']);



});
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    Route::get('/admin/daily-attendance', [AdminReportController::class, 'dailyAttendance']);
    Route::get('/admin/monthly-attendance', [AdminReportController::class, 'monthlyAttendance']);
    Route::get('/admin/student-report/{id}', [AdminReportController::class, 'studentReport']);
    Route::get('/admin/top-students', [AdminReportController::class, 'topStudents']);

});
// Fee routes
Route::post('/fees/pay', [FeeController::class, 'payFee']);
Route::get('/fees/student/{id}', [FeeController::class, 'studentFees']);
Route::get('/fees/pending', [FeeController::class, 'pendingFees']);

// Report routes
Route::get('/reports/pending-fees', [ReportController::class, 'pendingFees']);
Route::get('/reports/paid-students', [ReportController::class, 'paidStudents']);
Route::get('/reports/monthly-collection', [ReportController::class, 'monthlyCollection']);
