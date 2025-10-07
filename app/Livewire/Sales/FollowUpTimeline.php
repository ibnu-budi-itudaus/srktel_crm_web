<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\FollowUp;
use Filament\Notifications\Notification;

class FollowUpTimeline extends Component
{
    public int $saleId;
    public $followUps = [];
    public $deleteId = null;

    //mode edit
    public $editingId = null;
    public $editDate;
    public $editResult;
    public $editStatus;


    protected $rules = [
        'editDate'   => 'required|date',
        'editResult' => 'required|string|min:3',
        'editStatus' => 'required|in:prospect,pending,deal,no_deal',
    ];

    protected $messages = [
        'editDate.required'   => 'Tanggal follow up wajib diisi.',
        'editResult.required' => 'Hasil follow up wajib diisi.',
        'editResult.min'      => 'Hasil follow up minimal 3 karakter.',
    ];

    public function mount(int $saleId)
    {
        $this->saleId = $saleId;
         $this->loadFollowUps();
    }

    public function loadFollowUps()
    {
        $this->followUps = FollowUp::where('sale_id', $this->saleId)
            ->orderByDesc('follow_up_date')
            ->get();
    }

     

    // dipanggil saat klik tombol hapus
    public function confirmDelete($id)
    {
        $this->deleteId = $id;

          // kirim event ke browser
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteFollowUp($id)
    {
        $followUp = FollowUp::find($id);

        if ($followUp) {
            $followUp->delete();

            $this->loadFollowUps();

            // 🔔 Notifikasi Filament
            Notification::make()
                ->title('Follow Up berhasil dihapus')
                ->success()
                ->send();

            
            // refresh timeline
            $this->dispatch('$refresh');
        }
    }

      public function editFollowUp($id)
    {
        $followUp = FollowUp::findOrFail($id);
        if ($followUp) {
            $this->editingId = $id;
            $this->editDate = $followUp->follow_up_date->format('Y-m-d');
            $this->editResult = $followUp->result;
            $this->editStatus = $followUp->status;
        }
    }

    public function updateFollowUp()
    {
        $this->validate();

        $followUp = FollowUp::find($this->editingId);

        if ($followUp) {
            $followUp->update([
                'follow_up_date' => $this->editDate,
                'result' => $this->editResult,
                 'status' => $this->editStatus,
            ]);

            $this->cancelEdit();
            $this->loadFollowUps();
            
            Notification::make()
                ->title('Follow Up berhasil diperbarui')
                ->success()
                ->send();

            $this->editingId = null;
            $this->dispatch('$refresh');
        }
    }

    public function cancelEdit()
    {
         $this->reset(['editingId', 'editDate', 'editResult', 'editStatus']);
    }

    

//     #[\Livewire\Attributes\On('deleteFollowUp')] 
// public function deleteFollowUp($id)
// {
//     $followUp = \App\Models\FollowUp::find($id);
//     if ($followUp) {
//         $followUp->delete();
//         $this->dispatch('follow-up-deleted'); // trigger JS notif
//     }
// }

    public function render()
    {
        return view('livewire.sales.follow-up-timeline', [
            'followUps' => FollowUp::where('sale_id', $this->saleId)->latest()->get(),
        ]);

        //     return view('livewire.sales.follow-up-timeline', [
        //     'followUps' => $this->sale->followUps()->latest()->get(),
        // ]);
    }
}
