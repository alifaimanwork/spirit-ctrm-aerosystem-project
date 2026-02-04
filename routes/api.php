<?php

use App\Http\Controllers\JobCardController;
use App\Http\Controllers\Api\PlcDataController;
use App\Http\Controllers\Api\SapDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Job Card API
Route::get('/jobcard', [JobCardController::class, 'getByJobOrderAndPartNo'])->name('api.jobcard.get');
Route::get('/job-card/next-unprocessed', [JobCardController::class, 'getNextJob'])->name('api.jobcard.next-unprocessed');

// SAP API
Route::prefix('sap')->group(function () {
    Route::get('/latest', [SapDataController::class, 'getLatest'])->name('api.sap.latest');
    Route::get('/match', [SapDataController::class, 'findMatch'])->name('api.sap.match');
});

// Live Production API - Using direct route definitions to avoid any prefix issues
Route::get('/hub-data/latest-job-card', [\App\Http\Controllers\Api\PlcDataController::class, 'getLatestJobCard'])->name('api.hub-data.latest-job-card');
Route::get('/hub-data/next-unprocessed', [\App\Http\Controllers\Api\PlcDataController::class, 'getNextUnprocessed'])->name('api.hub-data.next-unprocessed');

// Actual Part Data API
Route::prefix('actual-part')->group(function () {
    Route::post('/data', [PlcDataController::class, 'store'])->name('api.actual-part.data.store');
    Route::get('/report', [PlcDataController::class, 'getReportData'])->name('api.actual-part.report');
    Route::get('/latest', [PlcDataController::class, 'getLatestCallIdData'])->name('api.actual-part.latest');
    Route::get('/{id}', [PlcDataController::class, 'getCallIdDataById'])->name('api.actual-part.by-id');
    Route::match(['get', 'post'], '/process-unprocessed', [PlcDataController::class, 'processUnprocessed'])->name('api.actual-part.process-unprocessed');
});

// Processed Data API
Route::prefix('processed-data')->group(function () {
    // GET endpoint to fetch processed data
    Route::get('/', [\App\Http\Controllers\ProcessedDataController::class, 'index']);
    
    // GET endpoint to check if job card was processed
    Route::get('/check', [\App\Http\Controllers\ProcessedDataController::class, 'check']);
    
    // POST endpoint to store processed data
    Route::post('/', [\App\Http\Controllers\ProcessedDataController::class, 'store'])->name('api.processed-data.store');
});
