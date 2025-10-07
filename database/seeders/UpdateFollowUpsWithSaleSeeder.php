<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\FollowUp;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UpdateFollowUpsWithSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FollowUp::whereNull('sale_id')->get()->each(function ($followUp) {
            $sale = Sale::where('customer_id', $followUp->customer_id)->first();
            if ($sale) {
                $followUp->sale_id = $sale->id;
                $followUp->save();
            }
        });
    }
}
