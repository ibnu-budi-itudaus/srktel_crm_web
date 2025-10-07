<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ForecastExport implements FromView, WithEvents
{

    protected $bulan, $tahun, $startDate, $endDate, $periode_bulan, $status, $customer_id;
    protected $sales, $maxFollowUps;
    protected $jumlahProspek, $totalDeal, $totalNoDeal, $totalPending;
    protected $dealPercent, $noDealPercent, $pendingPercent, $total, $judul;

    public function __construct($status = null, $customer_id = null, $bulan = null, $tahun = null, $startDate = null, $endDate = null, $periode_bulan = null)
    {
        // normalisasi bulan
        if (is_array($bulan)) {
            $bulan = $bulan['value'] ?? null;
        }
        if ($bulan && !is_numeric($bulan)) {
            try {
                $bulan = Carbon::parse($bulan)->month;
            } catch (\Exception $e) {
                $bulan = now()->month;
            }
        }

        $this->bulan     = $bulan ? (int) $bulan : null;
        $this->tahun     = request()->input('tahun.value'); 
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->periode_bulan = $periode_bulan;
         $this->status = $status;
        $this->customer_id = $customer_id;
    }

    

    public function view(): View
    {
        // Query sekali saja
        $query = Sale::with(['customer', 'followUps', 'project']);
        if ($this->bulan) $query->whereMonth('created_at', $this->bulan);
        if ($this->tahun) {

            $query->whereYear('created_at', $this->tahun);
        }
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }
        if ($this->periode_bulan) {
           [$year, $month] = explode('-', $this->periode_bulan);
        $query->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);

        }
         if ($this->status) {

            $query->where('status', $this->status);
        }
         if ($this->customer_id) {

            $query->where('customer_id', $this->customer_id);
        }


        $this->sales        = $query->get();
        $this->maxFollowUps = $this->sales->map(fn($s) => $s->followUps->count())->max() ?? 0;

        $this->jumlahProspek = $this->sales->count();
        $this->totalDeal     = $this->sales->where('status', 'deal')->count();
        $this->totalNoDeal   = $this->sales->where('status', 'no_deal')->count();
        $this->totalPending  = $this->sales->where('status', 'pending')->count();

        $this->dealPercent   = $this->jumlahProspek > 0 ? round(($this->totalDeal / $this->jumlahProspek) * 100) : 0;
        $this->noDealPercent = $this->jumlahProspek > 0 ? round(($this->totalNoDeal / $this->jumlahProspek) * 100) : 0;
        $this->pendingPercent= $this->jumlahProspek > 0 ? round(($this->totalPending / $this->jumlahProspek) * 100) : 0;

        $this->total = $this->sales->sum('price');


        if ($this->tahun && $this->bulan) {
            $tahun = request()->input('tahun.value'); 
            $this->judul = "Forecast Penjualan " . 
                Carbon::create()->locale('id')->month($this->bulan)->translatedFormat('F') . 
                " {$tahun}";
        } elseif ($this->bulan) {
            $this->judul = "Forecast Penjualan " . 
                Carbon::create()->locale('id')->month($this->bulan)->translatedFormat('F');
        } elseif ($this->tahun) {
            $tahun = request()->input('tahun.value'); 
            $this->judul = "Forecast Penjualan Tahun {$tahun}";
        } elseif ($this->startDate && $this->endDate) {
            $this->judul = "Forecast Penjualan Periode " . 
                Carbon::parse($this->startDate)->format('d M Y') . " s/d " .
                Carbon::parse($this->endDate)->format('d M Y');

        } else {
            $this->judul = "Akumulasi Forecast Penjualan " . 
                Carbon::now()->locale('id')->translatedFormat('F Y');
        }

        if ($this->periode_bulan) {
        [$year, $month] = explode('-', $this->periode_bulan);
        $this->judul = "Forecast Penjualan " .
            Carbon::create()->locale('id')->month($month)->translatedFormat('F') .
            " {$year}";
    } elseif ($this->tahun && $this->bulan) {
        $this->judul = "Forecast Penjualan " .
            Carbon::create()->locale('id')->month($this->bulan)->translatedFormat('F') .
            " {$this->tahun}";
    } elseif ($this->bulan) {
        $this->judul = "Forecast Penjualan " .
            Carbon::create()->locale('id')->month($this->bulan)->translatedFormat('F');
    } elseif ($this->tahun) {
        $this->judul = "Forecast Penjualan Tahun {$this->tahun}";
    } elseif ($this->startDate && $this->endDate) {
        $this->judul = "Forecast Penjualan Periode " .
            Carbon::parse($this->startDate)->format('d M Y') . " s/d " .
            Carbon::parse($this->endDate)->format('d M Y');
    } else {
        $this->judul = "Akumulasi Forecast Penjualan " .
            Carbon::now()->locale('id')->translatedFormat('F Y');
    }
        

        return view('exports.forecast-excel', [
            'status'         => $this->status,
            'customer_id'    => $this->customer_id,
            'sales'          => $this->sales,
            'maxFollowUps'   => $this->maxFollowUps,
            'judul'          => $this->judul,
            'total'          => $this->total,
            'jumlahProspek'  => $this->jumlahProspek,
            'totalDeal'      => $this->totalDeal,
            'dealPercent'    => $this->dealPercent,
            'totalNoDeal'    => $this->totalNoDeal,
            'noDealPercent'  => $this->noDealPercent,
            'totalPending'   => $this->totalPending,
            'pendingPercent' => $this->pendingPercent,
        ]);
    }

    private function columnName($index)
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr($index % 26 + 65) . $letters;
            $index   = floor($index / 26) - 1;
        }
        return $letters;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Auto-size kolom kecuali FU hasil
                foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
                    if (!in_array($col, ['A','B','K','M','O','Q','S','U','W','Y','AA','AC','AE', 'AG', 'AI', 'AK', 'AM', 'AO'])) {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    }
                }

                

                // Kolom FU: lebar tetap
                for ($i = 1; $i <= $this->maxFollowUps; $i++) {
                    $colTanggal = $this->columnName(9 + ($i-1)*2);
                    $colHasil   = $this->columnName(10 + ($i-1)*2);
                    $sheet->getColumnDimension($colTanggal)->setWidth(12);
                    $sheet->getColumnDimension($colHasil)->setWidth(50);
                    $sheet->getStyle("{$colHasil}:{$colHasil}")->getAlignment()->setWrapText(true);
                }

                // Warna baris sesuai status
                $highestCol = $sheet->getHighestDataColumn();
                $rowStart   = 3;
                $rowEnd     = $sheet->getHighestRow();
                for ($row = $rowStart; $row <= $rowEnd; $row++) {
                    $status = strtolower((string) $sheet->getCell("H{$row}")->getValue());
                    $color = null;
                    if ($status === 'deal') $color = 'C6EFCE';
                    elseif ($status === 'pending') $color = 'DDEBF7';
                    elseif ($status === 'no_deal') $color = 'FFF2CC';

                    if ($color) {
                        $sheet->getStyle("A{$row}:{$highestCol}{$row}")->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setARGB($color);
                    }
                }

                // Batasi hanya kolom A mulai dari baris 3 (tabel utama)
            foreach (range(2, $rowEnd) as $row) {
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(6);
            }

            // Format tanggal agar lebih rapih
            $sheet->getStyle("A3:A{$rowEnd}")
                  ->getNumberFormat()
                  ->setFormatCode('dd-mm-yyyy');
    

                // Border all
                $range = "A2:{$highestCol}{$rowEnd}";
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'wrapText'   => true,
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $rangeT = "A1:{$highestCol}A1";
                $sheet->getStyle($rangeT)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);


                // Row height default
                foreach ($sheet->getRowIterator() as $row) {
                    $sheet->getRowDimension($row->getRowIndex())->setRowHeight(45);
                }

                // Rekap hasil (setelah tabel)
                $rekapRow = $rowEnd + 3;

                $rekapData = [
                    ['Jumlah Prospek :', "{$this->jumlahProspek} ", null],
                    ['Total Deal :', "{$this->totalDeal} ({$this->dealPercent}%)", 'C6EFCE'],
                    ['Pending / Belum ada jawaban pasti :', "{$this->totalPending} ({$this->pendingPercent}%)", 'DDEBF7'],
                    ['No Deal :', "{$this->totalNoDeal} ({$this->noDealPercent}%)", 'FFF2CC'],
                ];

                foreach ($rekapData as $i => [$label, $value, $fill]) {
                    $row = $rekapRow + $i;
                    $sheet->mergeCells("A{$row}:B{$row}");
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("C{$row}", $value);

                    $secondRowTable2 = $rekapRow + 2;

                    $sheet->getRowDimension($secondRowTable2)->setRowHeight(40);

                    $style = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => '000000'],
                            ],
                        ],

                        'alignment' => [
                           // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText'   => true,
                        ],

                    ];
                    if ($fill) {
                        $style['fill'] = [
                            'fillType' => Fill::FILL_SOLID,
                            'color'    => ['rgb' => $fill],
                        ];
                    }
                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($style);
                }
            },
        ];
    }
}


