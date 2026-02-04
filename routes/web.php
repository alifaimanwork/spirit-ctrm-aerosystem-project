<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\DataRecordController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FetchDataController;
use App\Http\Controllers\HubReportDataController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\ProcessedDataController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// Temporary route to view logs - REMOVE IN PRODUCTION
Route::get('/view-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    
    if (!file_exists($logFile)) {
        return 'Log file not found!';
    }
    
    $logs = file_get_contents($logFile);
    
    // Try to convert encoding if needed
    if (!mb_check_encoding($logs, 'UTF-8')) {
        $logs = mb_convert_encoding($logs, 'UTF-8', 'auto');
    }
    
    echo '<pre>' . htmlspecialchars($logs) . '</pre>';
    exit;
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended(route('dashboard', absolute: false));
    }

    return redirect()->intended(route('login', absolute: false));
});

// Production Report
Route::get('/production-report', function () {
    return Inertia::render('ProductionReport');
})->middleware(['auth', 'verified'])->name('production.report');

// Production Report Data
Route::get('/api/production-report', [HubReportDataController::class, 'generateProductionReport'])
    ->middleware(['auth', 'verified'])
    ->name('production.report.data');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Live Production routes - Updated to use Hub naming
    Route::get('/api/get-next-actual-part', [HubController::class, 'getNextActualPart'])->name('hub.next');
    Route::get('/hub-report-data', [HubReportDataController::class, 'index'])->name('hub.report.data');
    Route::get('/check-sap-match', [ComparisonController::class, 'checkMatch'])->name('sap.check');
    Route::get('/api/get-next-job', [JobCardController::class, 'getNextJob'])->name('jobcard.next');
    Route::get('/api/find-sap-match', [ComparisonController::class, 'findSapMatch'])->name('sap.find');
    Route::post('/store-comparison', [ComparisonController::class, 'store'])->name('comparison.store');
    
    // Processed Data routes
    Route::get('/api/check-processed', [ProcessedDataController::class, 'checkProcessed'])->name('processed.check');
    Route::post('/api/mark-processed', [ProcessedDataController::class, 'markAsProcessed'])->name('processed.mark');
    
    // Dashboard and other routes
    Route::get('/dashboard', [FetchDataController::class, 'index'])->name('dashboard');
    Route::get('/liveproduction', function () {
        return Inertia::render('LiveProduction');
    })->name('liveproduction');
    Route::get('/resultlog', function () {
        return Inertia::render('ResultLog');
    })->name('resultlog');

    // Job Card route - moved outside auth for testing
    // Removed from here

    // Data record routes
    Route::get('/data-records', [DataRecordController::class, 'index'])->name('data.records');

    // File upload routes
    Route::post('/upload-sap', [FileUploadController::class, 'uploadSap'])->name('sap.upload');
    Route::get('/sap-data', [FileUploadController::class, 'getsapData'])->name('sap.data');

    // Comparison results route
    Route::get('/comparison-results', [ComparisonController::class, 'getResults'])->name('comparison.results');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';