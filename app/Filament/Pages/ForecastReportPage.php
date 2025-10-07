<?php

namespace App\Filament\Pages;

use UnitEnum;
use BackedEnum;
use Carbon\Carbon;
use App\Models\Sale;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ForecastReportPage extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected string $view = 'filament.pages.forecast-report-page';
    protected static ?string $title = 'Forecast Report';
    protected static string |UnitEnum| null $navigationGroup = 'Reports';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->with(['customer.source', 'followUps', 'project'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->sortable()
                    ->date(),

                Tables\Columns\TextColumn::make('id')
                    ->label('No.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Nama PIC')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('Nama Perusahaan'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Proyek')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Nilai')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('No HP')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'prospect',
                        'info'    => 'pending',
                        'success' => 'deal',
                        'danger'  => 'no_deal',
                    ])
                    ->label('Pipeline Status')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.source.name')
                    ->label('Sumber')
                    ->sortable(),

                   
            ])
            
            

            
           ->filters([
    Tables\Filters\SelectFilter::make('status')
        ->options([
            'deal' => 'Deal',
            'no_deal' => 'No Deal',
            'pending' => 'Pending',
            'prospect' => 'Prospect',
        ]),
         
    Tables\Filters\SelectFilter::make('customer_id')
        ->label('PIC')
        ->relationship('customer', 'name'),

     Tables\Filters\SelectFilter::make('tahun')
                ->label('Tahun')
                ->options(function (): array {
                    $years = [];
                    // Ambil tahun dari data yang sudah ada di tabel Anda
                   $minDateString = Sale::min('created_at'); // Mengembalikan string
                    
                    // Periksa jika ada tanggal, lalu parse menjadi objek Carbon
                    if ($minDateString) {
                        $minYear = Carbon::parse($minDateString)->format('Y');
                    } else {
                        $minYear = now()->year - 5; // Default jika tidak ada data
                    }

                    for ($year = now()->year; $year >= $minYear; $year--) {
                        $years[$year] = $year;
                    }
                    return $years;
                })
                ->query(function (Builder $query, array $data): Builder {
                    if (isset($data['value'])) {
                        $query->whereYear('created_at', $data['value']);
                    }
                    return $query;
                }),
          

        Tables\Filters\SelectFilter::make('bulan')
                ->label('Bulan')
                ->options([
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (isset($data['value'])) {
                        $query->whereMonth('created_at', $data['value']);
                    }
                    return $query;
                }),

            //     Tables\Filters\SelectFilter::make('periode_bulan')
            //     ->label('Periode (Bulan & Tahun)')
            //     ->options(function (): array {
            //         $options = [];

            //         $minDateString = Sale::min('created_at');
            //         $minYear = $minDateString ? \Carbon\Carbon::parse($minDateString)->year : now()->year - 5;

            //         $bulanIndo = [
            //             1 => 'Januari',
            //             2 => 'Februari',
            //             3 => 'Maret',
            //             4 => 'April',
            //             5 => 'Mei',
            //             6 => 'Juni',
            //             7 => 'Juli',
            //             8 => 'Agustus',
            //             9 => 'September',
            //             10 => 'Oktober',
            //             11 => 'November',
            //             12 => 'Desember',
            //         ];

            //         // Loop tahun → bulan
            //         for ($year = now()->year; $year >= $minYear; $year--) {
            //             foreach ($bulanIndo as $num => $nama) {
            //                 $key = $year . '-' . str_pad($num, 2, '0', STR_PAD_LEFT);
            //                 $options[$key] = $year . ' - ' . $nama;
            //             }
            //         }

            //         return $options;
            //     })
            //     ->modifyQueryUsing(function (Builder $query, $state) {
            //     if (isset($state['value'])) {
            //         [$year, $month] = explode('-', $state['value']);
            //         $query->whereYear('created_at', $year)
            //             ->whereMonth('created_at', $month);
            //     }
            // }),

                Tables\Filters\Filter::make('periode')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($q, $date) => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn ($q, $date) => $q->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                // Action::make('exportExcel')
                //     ->label('Download Excel')
                //     ->url(route('reports.forecast.excel', request()->all()))
                //     ->openUrlInNewTab(),

                Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn() => route('forecast-report.export-excel', $this->getTableFiltersForm()->getState()))
                    ->openUrlInNewTab(),

                Action::make('printReport')
                    ->label('Print')
                    ->url(fn() => route('reports.forecast.print', $this->getTableFiltersForm()->getState()))
                    ->openUrlInNewTab()
                     ->icon('heroicon-o-printer'),

            
                Action::make('print_laporan')
                    ->label('Download PDF')
                    ->url(fn () => route('forecast-report.print-report', $this->getTableFiltersForm()->getState()))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document-text'),
        

                // Action::make('exportPdf')
                //     ->label('Download PDF')
                //     ->icon('heroicon-o-document-text')
                //     ->url(route('reports.forecast.pdf', request()->all()))
                //     ->openUrlInNewTab(),

                    
            ])

            ->recordActions([
                ViewAction::make()
                        ->label('Detail')
                        ->modalHeading('Detail Forecast')
                         ->modalContent(function ($record) {
                    $record->load(['customer', 'project', 'followUps']); // load relasi
                    return view('tables.rows.forecast-detail', [
                        'record' => $record,
                    ]);
                }),
                ])

               ->paginated([10, 25, 50]) // pilihan jumlah data per halaman
        ->defaultPaginationPageOption(10)
        ->striped() // opsional: buat baris tabel bergaris
            ;
            
            
    }

    protected function getHeaderWidgets(): array
    {
        return [
           
        ];
    }

//     public function printLaporan()
// {
//     // Ambil filter yang sedang aktif
//     $filters = $this->getTableFiltersForm()->getState();

//     // Redirect ke route print
//     return redirect()->route('forecast-report.print', $filters);
// }
}
