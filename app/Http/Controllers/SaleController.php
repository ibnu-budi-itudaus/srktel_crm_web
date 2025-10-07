<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class SaleController extends Controller
{
    public function updateStatus(Sale $sale, Request $request)
    {
        $sale->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
             'message' => "Proyek {$sale->project?->name} ({$sale->customer?->name}) dipindahkan ke {$sale->status}",
        ]);
    }
}
