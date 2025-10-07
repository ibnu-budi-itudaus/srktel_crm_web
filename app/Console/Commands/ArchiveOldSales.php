<?php

namespace App\Console\Commands;

use App\Models\Sale;
use Illuminate\Console\Command;

class ArchiveOldSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:archive-old {months=2 : Jumlah bulan untuk cutoff}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arsipkan sales deal/no_deal yang sudah lebih dari X bulan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = (int) $this->argument('months');
        $cutoff = now()->subMonths($months);

        $sales = Sale::whereIn('status', ['deal', 'no_deal'])
            ->whereNull('archived_at')
            ->where('updated_at', '<=', $cutoff)
            ->get();

        foreach ($sales as $sale) {
            $sale->archive();
        }

        $this->info("Berhasil mengarsipkan {$sales->count()} sales.");
    }
}
