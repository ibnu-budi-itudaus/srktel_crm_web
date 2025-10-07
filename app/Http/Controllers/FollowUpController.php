<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function destroy($id)
    {
        $followUp = FollowUp::findOrFail($id);
        $followUp->delete();

        return redirect()->back()->with('success', 'Follow up berhasil dihapus.');
    }
}
