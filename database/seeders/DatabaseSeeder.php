<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\FollowUp; // ✅ import model FollowUp
use App\Models\Sale;     // ✅ import model Sale

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        FollowUp::whereNull('sale_id')->get()->each(function ($followUp) {
            $sale = Sale::where('customer_id', $followUp->customer_id)
                        ->latest('created_at') // ambil sale terbaru biar lebih relevan
                        ->first();

            if ($sale) {
                $followUp->sale_id = $sale->id;
                $followUp->save();
            }
        });
    }
}
