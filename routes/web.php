<?php

use App\Models\Sale;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ForecastReportController;
use App\Exports\ForecastExport;
use Maatwebsite\Excel\Facades\Excel;


// routes/web.php
Route::get('/', function(){
    return redirect()->route('filament.admin.auth.login');
});


Route::post('/sales/{sale}/update-status', [SaleController::class, 'updateStatus']);
Route::post('/kanban/update-status', [KanbanController::class, 'updateStatus']);
Route::get('/sales/{sale}', function (Sale $sale) {
    return response()->json([
        'id' => $sale->id,
        'price' => $sale->price,
        'status' => $sale->status,
        'project' => $sale->project,
        'customer' => $sale->customer,
        'all_projects' => \App\Models\Project::select('id','name')->get(),
    ]);
});
Route::put('/sales/{sale}', function (Sale $sale, Illuminate\Http\Request $request) {
    $sale->update($request->only('project_id', 'price', 'status'));
    return response()->json(['message' => 'Data berhasil diperbarui!']);
});

Route::delete('/followup/{id}', [FollowUpController::class, 'destroy'])
    ->name('followup.delete');

Route::get('/reports/forecast/export-excel', [ForecastReportController::class, 'exportExcel'])
    ->name('reports.forecast.excel');

Route::get('/reports/forecast/print', [ForecastReportController::class, 'printView'])
    ->name('reports.forecast.print');


Route::get('/forecast-report/print-report', [ForecastReportController::class, 'print_report'])
    ->name('forecast-report.print-report');


Route::get('/reports/forecast/pdf', [ForecastReportController::class, 'exportPdf'])->name('reports.forecast.pdf');

Route::prefix('forecast')->group(function () {
    Route::get('/', [ForecastReportController::class, 'index'])->name('forecast.index');
    Route::get('/print', [ForecastReportController::class, 'print'])->name('forecast.print');
    Route::get('/pdf', [ForecastReportController::class, 'pdf'])->name('forecast.pdf');
});

Route::get('/forecast-report/export-excel', [ForecastReportController::class, 'exportMsExcel'])
    ->name('forecast-report.export-excel');

// Route::get('/forecast-report/export-excel', function (\Illuminate\Http\Request $request) {
//     return Excel::download(new ForecastExport(
//         $request->input('bulan.value'),
//         $request->input('tahun.value'),
//         $request->input('start_date'),
//         $request->input('end_date'),
//         5 // max follow up, bisa diset sesuai kebutuhan
//     ), 'forecast-report.xlsx');
// })->name('forecast-report.export-excel');