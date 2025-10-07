<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class KanbanController extends Controller
{
    public function updateStatus(Request $request)
    {
        $sale = Sale::findOrFail($request->id);
        $sale->status = $request->status;
        $sale->save();

        return response()->json(['success' => true]);
    }
}
