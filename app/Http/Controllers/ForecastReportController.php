<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Exports\ForecastExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ForecastReportController extends Controller
{
     private function filterSales(Request $request)
    {
        $query = Sale::with(['customer.source', 'project', 'followUps']);

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }


     public function index(Request $request)
    {
        $sales = $this->filterSales($request);

        // hitung FU terbanyak agar tabel konsisten
        $maxFollowUps = $sales->map(fn($s) => $s->followUps->count())->max() ?? 0;

        return view('forecast.index', compact('sales', 'maxFollowUps'));
    }

     public function print(Request $request)
    {
        $sales = $this->filterSales($request);
        $maxFollowUps = $sales->map(fn($s) => $s->followUps->count())->max() ?? 0;

        return view('forecast.print', compact('sales', 'maxFollowUps'));
    }

    public function pdf(Request $request)
    {
        $sales = $this->filterSales($request);
        $maxFollowUps = $sales->map(fn($s) => $s->followUps->count())->max() ?? 0;

        $pdf = Pdf::loadView('forecast.pdf', compact('sales', 'maxFollowUps'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('forecast-report.pdf');
    }

   

    // Export Excel
    public function exportExcel()
    {
        return Excel::download(new ForecastExport, 'forecast-report.xlsx');
    }

    // Print (tampilkan view untuk cetak)
    public function printView(Request $request)
    {
        $query = Sale::with(['customer.source', 'project']);

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->get('tahun'));
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->get('bulan'));
        }

        // Filter periode (dari-sampai)
       $periode = $request->input('periode', []);

        if (!empty($periode['from'])) {
            $query->whereDate('created_at', '>=', $periode['from']);
        }

        if (!empty($periode['until'])) {
            $query->whereDate('created_at', '<=', $periode['until']);
        }

        // Ambil data
        $sales = $query->get();
         // Hitung jumlah prospek (total baris sales)
        $jumlahProspek = $sales->count();


        // Hitung status
        $totalDeal   = $sales->where('status', 'deal')->count();
        $totalNoDeal = $sales->where('status', 'no_deal')->count();
        $totalPending = $sales->where('status', 'pending')->count(); // atau 'Pending'

        $bulan = $request->filled('bulan.value') ? (int) $request->input('bulan.value') : null;
        $tahun = $request->input('tahun.value');
         $startDate = $request->input('periode.from');
        $endDate = $request->input('periode.until');

        // Hitung persentase
        $dealPercent   = $jumlahProspek > 0 ? round(($totalDeal / $jumlahProspek) * 100) : 0;
        $noDealPercent = $jumlahProspek > 0 ? round(($totalNoDeal / $jumlahProspek) * 100) : 0;
        $pendingPercent = $jumlahProspek > 0 ? round(($totalPending / $jumlahProspek) * 100) : 0;
        $maxFollowUps = $sales->map(function ($sale) {
            return $sale->followUps->count();
        })->max() ?? 0;

        return view('reports.forecast_print', compact('sales', 'maxFollowUps', 'jumlahProspek', 'totalDeal', 'totalNoDeal', 'totalPending', 'dealPercent', 'noDealPercent', 'pendingPercent', 'bulan', 'tahun', 'startDate', 'endDate'));
    }

    public function exportPdf()
{
    $sales = Sale::with(['customer.source', 'followUps', 'project'])->get();
    $maxFollowUps = $sales->map(fn($s) => $s->followUps->count())->max();

    $pdf = PDF::loadView('reports.forecast_pdf', compact('sales', 'maxFollowUps'))
        ->setPaper('a4', 'landscape');

    return $pdf->download('forecast-report.pdf');
}

public function print_laporan(Request $request)
{
    $query = Sale::query();

    // Filter Bulan & Tahun
    if ($request->has('tableFilters')['periode']['value'] ?? false) {
        [$year, $month] = explode('-', $request->input('tableFilters.periode.value'));
        $query->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);
    }

    // Filter Rentang Tanggal
    if ($request->input('tableFilters.periode.from')) {
        $query->whereDate('created_at', '>=', $request->input('tableFilters.periode.from'));
    }
    if ($request->input('tableFilters.periode.until')) {
        $query->whereDate('created_at', '<=', $request->input('tableFilters.periode.until'));
    }

    $sales = $query->with(['customer','project'])->get();

    return view('forecast.print_laporan', compact('sales'));
}

 public function print_report(Request $request)
    {
        $query = Sale::with(['customer.source', 'project']);

        // Filter periode (dari-sampai)
       $periode = $request->input('periode', []);

        if (!empty($periode['from'])) {
            $query->whereDate('created_at', '>=', $periode['from']);
        }

        if (!empty($periode['until'])) {
            $query->whereDate('created_at', '<=', $periode['until']);
        }

       
        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }

         if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }


        // Ambil data
        $sales = $query->get();
         // Hitung jumlah prospek (total baris sales)
        $jumlahProspek = $sales->count();

        // Hitung status
        $totalDeal   = $sales->where('status', 'deal')->count();
        $totalNoDeal = $sales->where('status', 'no_deal')->count();
        $totalPending = $sales->where('status', 'pending')->count(); // atau 'Pending'

        // Hitung persentase
        $dealPercent   = $jumlahProspek > 0 ? round(($totalDeal / $jumlahProspek) * 100) : 0;
        $noDealPercent = $jumlahProspek > 0 ? round(($totalNoDeal / $jumlahProspek) * 100) : 0;
        $pendingPercent = $jumlahProspek > 0 ? round(($totalPending / $jumlahProspek) * 100) : 0;
        $maxFollowUps = $sales->map(function ($sale) {
            return $sale->followUps->count();
        })->max() ?? 0;
        

        // return view('reports.forecast-print', [
        //     'sales' => $sales,
        //     'maxFollowUps' =>  $maxFollowUps,
        //     'filters' => $request->all(),
        // ]);

        $pdf = PDF::loadView('reports.forecast-print', [
        'sales'       => $sales,
        'bulan'       => $request->filled('bulan.value') ? (int) $request->input('bulan.value') : null,
        'tahun'       => $request->input('tahun.value'),
        'startDate'   => $request->input('periode.from'),
        'endDate'     => $request->input('periode.until'),
        'status'       => $request->input('status'),
        'customer_id'  => $request->input('customer_id'),
        'maxFollowUps'=> $maxFollowUps,
         'jumlahProspek' => $jumlahProspek,
            'totalDeal'     => $totalDeal,
            'dealPercent'   => $dealPercent,
            'totalNoDeal'   => $totalNoDeal,
            'noDealPercent' => $noDealPercent,
            'totalPending'  => $totalPending,
            'pendingPercent'=> $pendingPercent,
    ])
        ->setPaper('a4', 'landscape');

    return $pdf->stream('forecast-report.pdf');

    //     return view('reports.forecast-print', [
    //     'sales'       => $sales,
    //     'bulan'       => $request->filled('bulan.value') ? (int) $request->input('bulan.value') : null,
    //     'tahun'       => $request->input('tahun.value'),
    //     'startDate'   => $request->input('start_date'),
    //     'endDate'     => $request->input('end_date'),
    //     'maxFollowUps'=> $maxFollowUps,
    // ]);


    }

    public function exportMsExcel(Request $request)
    {
        return Excel::download(
            new ForecastExport(
                $request->status,
                $request->customer_id,
                $request->bulan,
                $request->tahun,
                $request->start_date,
                $request->end_date
            ),
            'forecast-report.xlsx'
        );
    }


}
