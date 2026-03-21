<?php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\FeeController;
use App\Http\Controllers\API\ReportController;

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

});
// Fee routes
Route::post('/fees/pay', [FeeController::class, 'payFee']);
Route::get('/fees/student/{id}', [FeeController::class, 'studentFees']);
Route::get('/fees/pending', [FeeController::class, 'pendingFees']);

// Report routes
Route::get('/reports/pending-fees', [ReportController::class, 'pendingFees']);
Route::get('/reports/paid-students', [ReportController::class, 'paidStudents']);
Route::get('/reports/monthly-collection', [ReportController::class, 'monthlyCollection']);