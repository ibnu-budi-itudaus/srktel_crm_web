<?php

namespace App\Filament\Resources\Sales\Pages;


use UnitEnum;
use Filament\Forms;
use Filament\Forms\Form; // Tetap diperlukan
use App\Models\Sale;
use App\Models\Sales;
use Filament\Actions;
use App\Models\Project;
use App\Models\Customer;
use App\Models\FollowUp;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\Sales\Widgets\SalesTabs;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;



class SalesPipeline extends Page implements Forms\Contracts\HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    

    protected static string $resource = \App\Filament\Resources\Sales\SaleResource::class;

    protected string $view = 'filament.resources.sales.pages.sales-pipeline';
//    protected static bool $shouldRegisterNavigation = true;
    // public bool $createModalOpen = false;
    // public $editModalOpen = false;
     public ?int $saleId = null; // <--- tambahkan ini
    public ?Sale $editingSale = null;
    public bool $showEditModal = false;
    // public $editingSale;
    public $sales;

    public $customer_id;
    public $project_id;
    public $price;
    public $status;

    // untuk follow up
    public ?Sale $followUpSale = null;

    public ?array $data = [];
    public ?array $followUpData = [];
    public ?int $editingFollowUpId = null;
    public $timelineSale;

      // Form instances
    // public ?Form $form = null;
    // public ?Form $followUpForm = null;
   


   protected $listeners = [
    'sale-updated' => '$refresh',
    'delete-follow-up' => 'deleteFollowUp',
];

    // public function mount(): void
    // {
    //     $this->form->fill();
    // }


//    protected function getForms(): array
// {
//     return [
//         'form' => $this->form(
//             \Filament\Forms\Form::make()
//                 ->schema($this->getFormSchema())
//                 ->statePath('data')
//         ),
//         'followUpForm' => $this->form(
//             \Filament\Forms\Form::make()
//                 ->schema($this->getFollowUpFormSchema())
//                 ->statePath('followUpData')
//         ),
//     ];
// }



     public function mount()
    {
        $this->sales = Sale::active()->with(['customer', 'project'])->whereNull('archived_at') // hanya ambil yang aktif
        ->get();
              // dump('Setting session to pipeline in SalesPipeline');
    
    session()->put('active_sales_tab', 'pipeline');
    
    // Debugging - hapus komentar untuk melihat nilai
    // dump('Session value after setting: ' . session('active_sales_tab'));
    }

    // protected function getForms(): array
    //     {
    //         return [
    //             'form',
    //             'followUpForm',
    //         ];
    //     }


    public function editSale($id): void
    {   
        $this->saleId = $id;
        $this->editingSale = Sale::findOrFail($id);

        // isi form Filament
        $this->form->fill($this->editingSale->toArray());
    
        // $this->showEditModal = true;
        $this->dispatch('open-modal', id: 'edit-sale'); // versi Livewire v3
    }

public function updateSale()
{
    if (! $this->editingSale) {
        return;
    }

    $data = $this->form->getState();

    $this->editingSale->update($data);

    $this->dispatch('close-modal', id: 'edit-sale');

    $this->reset(['editingSale', 'saleId']);

   Notification::make()
    ->title('Deal berhasil diperbarui')
    ->success()
    ->send();

}

    public function openFollowUpModal(int $saleId): void
{
    // Ambil sale yang sedang difollow-up
    $this->followUpSale = Sale::findOrFail($saleId);

    // Reset state form follow up supaya kosong
    //$this->reset('followUpData');

    // Atau bisa pakai ini untuk isi default value
        $this->followUpData = [
        'follow_up_date' => now()->format('Y-m-d'),
        'result' => '',
        ];
        $this->dispatch('open-modal', id: 'open-follow-up-modal');
}

public function openFollowUpTimeline($saleId)
{
    $this->timelineSale = Sale::with('followUps')->findOrFail($saleId);

    $this->dispatch('open-modal', id: 'follow-up-timeline-modal');
}


public function editFollowUp($id)
{
    $followUp = FollowUp::find($id);
    if ($followUp) {
        $this->editingFollowUpId = $id;
        $this->followUpData = [
            'follow_up_date' => $followUp->follow_up_date->format('Y-m-d'),
            'result' => $followUp->result,
            'status' => $followUp->status,
        ];

        $this->dispatch('open-modal', id: 'edit-follow-up-modal');
    }
}


public function updateFollowUp()
{
    $this->validate([
        'followUpData.follow_up_date' => 'required|date',
        'followUpData.result' => 'required|string|max:500',
        'followUpData.status' => 'required|in:prospect,pending,deal,no_deal',
    ]);

    $followUp = FollowUp::find($this->editingFollowUpId);
    if ($followUp) {
        $followUp->update($this->followUpData);
    $sale = Sale::find($this->editingFollowUpId);
    if ($sale) {
        $sale->update(['status' => $this->followUpData['status']]);
    }

        Notification::make()
            ->title('Follow Up berhasil diperbarui')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'edit-follow-up-modal');
        $this->refreshTimeline();
    }
}

public function archiveSale($saleId)
{
    $sale = Sale::find($saleId);

    if ($sale) {
        $sale->archive();
    }
             Notification::make()
            ->title("Sale {$sale->project?->name} berhasil diarsipkan")
            ->success()
            ->send();

        // refresh pipeline data
        $this->sales = Sale::active()->with(['customer', 'project'])->get();
        $this->dispatch('sale-updated');
    
}



    
     public function saveSale(): void
    {
        if ($this->editingSale) {
            $this->editingSale->update($this->form->getState());
        }

        $this->showEditModal = false;

        $this->dispatch('notify', [
            'title' => 'Sale updated!',
            'status' => 'success',
        ]);
    }

    public function saveFollowUp(): void
    {
        if (! $this->followUpSale) {
            return;
        }
        
       // validasi minimal (bisa ditambahkan)
    $this->validate([
        'followUpData.follow_up_date' => 'required|date',
        'followUpData.result' => 'required|string',
        'followUpData.status' => 'required|in:prospect,pending,deal,no_deal',
    ]);

    FollowUp::create([
        'sale_id' => $this->followUpSale->id,
        'follow_up_date' => $this->followUpData['follow_up_date'],
        'result' => $this->followUpData['result'],
        'status' => $this->followUpData['status'],
        'customer_id' => $this->followUpSale->customer_id, // auto-fill kalau perlu
    ]);

    $sale = Sale::find($this->followUpSale->id);
    if ($sale) {
        $sale->update(['status' => $this->followUpData['status']]);
    }


    Notification::make()
        ->title('Follow Up berhasil ditambahkan')
        ->success()
        ->send();

    $this->reset(['followUpSale', 'followUpData']);
    $this->dispatch('close-modal', id: 'open-follow-up-modal');

    // refresh data jika perlu
    $this->sales = Sale::with(['customer','project','followUps'])->get();
    }

//    public function deleteFollowUp(int $followUpId): void
// {
//     \App\Models\FollowUp::findOrFail($followUpId)->delete();

//     $this->dispatch('sale-updated');

//     \Filament\Notifications\Notification::make()
//         ->title('Follow Up berhasil dihapus')
//         ->success()
//         ->send();
// }

public function refreshTimeline()
{
    if ($this->timelineSale) {
        $this->timelineSale->refresh();
    }
}

public function deleteFollowUp($id)
{
    $followUp = FollowUp::find($id);
    if ($followUp) {
        $followUp->delete();

        Notification::make()
            ->title('Follow Up berhasil dihapus')
            ->success()
            ->send();

        $this->refreshTimeline();
    }
}

 public function getStatusesProperty()
    {
        return Sale::with('customer', 'project', 'followUps')->whereNull('archived_at') // hanya ambil yang aktif
                ->get();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('customer_id')
                ->label('Customer')
                ->options(\App\Models\Customer::pluck('name', 'id'))
                ->required(),

            Forms\Components\Select::make('project_id')
                ->label('Project')
                ->options(\App\Models\Project::pluck('name', 'id'))
                ->required(),

            Forms\Components\TextInput::make('price')
                ->label('Harga')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'prospect' => 'Prospect',
                    'pending'  => 'Pending',
                    'deal'     => 'Deal',
                    'no_deal'  => 'No Deal',
                    'cancel'   => 'Cancel',
                ])
                ->required(),
        ];
    }

     protected function getFollowUpFormSchema(): array
    {
        return [
            Forms\Components\DatePicker::make('follow_up_date')
                ->label('Tanggal Follow Up')
                ->required(),

            Forms\Components\Textarea::make('result')
                ->label('Catatan Hasil')
                ->required()
                ->rows(3),
        ];
    }

    // Tambahkan metode ini
//   protected function getForms(): array
// {
//     return [
//         'form' => $this->form(
//             Form::make()
//                 ->schema($this->getFormSchema())
//                 ->statePath('data')
//         ),
//         'followUpForm' => $this->form(
//             Form::make()
//                 ->schema($this->getFollowUpFormSchema())
//                 ->statePath('followUpData')
//         ),
//     ];
// }







 // --- Tombol Tambah ---
    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('tambahSale')
                ->label('New Deal')
                ->color('primary')
                ->modalHeading('Tambah Sale Baru')
                ->modalSubmitActionLabel('Simpan')
                ->form($this->getFormSchema())
                ->action(function (array $data): void {
                    Sale::create($data);

                    Notification::make()
                        ->title('Sale berhasil ditambahkan')
                        ->success()
                        ->send();

                    $this->dispatch('sale-updated');
                }),
        ];
    }
//-- Tombol Edit --
    public function getCardActions(Sale $sale): array
    {
        return [
            Actions\Action::make("editSale-{$sale->id}")
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->color('warning')
                ->button()
                ->modalHeading("Edit Sale: {$sale->project?->name}")
                ->modalSubmitActionLabel('Update')
                ->form($this->getFormSchema())
                ->mountUsing(fn ($action) => $action->form->fill([
                    'customer_id' => $sale->customer_id,
                    'project_id'  => $sale->project_id,
                    'price'       => $sale->price,
                    'status'      => $sale->status,
                ]))
                ->action(function (array $data) use ($sale) {
                    $sale->update($data);

                    Notification::make()
                        ->title('Sale berhasil diperbarui')
                        ->success()
                        ->send();

                    $this->dispatch('sale-updated');
                }),

                    
            // Action Follow Up - Menggunakan modal custom
            Actions\Action::make("followUp-{$sale->id}")
                ->label('Follow Up')
                ->icon('heroicon-o-phone')
                ->color('success')
                ->button()
                ->action(function () use ($sale) {
                    // Panggil metode untuk membuka modal follow up
                    $this->openFollowUpModal($sale->id);
                }),

            // Actions\Action::make("archiveSale-{$sale->id}")
            //     ->label('Archive')
            //     ->icon('heroicon-o-archive-box')
            //     ->color('secondary')
            //     ->requiresConfirmation()
            //     ->action(function () use ($sale) {
            //             $sale->archive(); // ✅ langsung pakai helper

            //             Notification::make()
            //                 ->title("Sale {$sale->project?->name} berhasil diarsipkan")
            //                 ->success()
            //                 ->send();

            //             // Refresh pipeline
            //             $this->sales = \App\Models\Sale::active()
            //                 ->with(['customer', 'project'])
            //                 ->get();

            //             $this->dispatch('sale-updated');
            //         }),

        ];
    }

    public function archiveSale1($saleId)
{
    $sale = Sale::find($saleId);

    if ($sale) {
        $sale->update(['archived_at' => now()]);

        Notification::make()
            ->title("Sale {$sale->project?->name} berhasil diarsipkan")
            ->success()
            ->send();

        $this->sales = Sale::active()->with(['customer', 'project'])->get();
        $this->dispatch('sale-updated');
    }
}
    
    // ===== FORM FOLLOW UP =====
  


    public function getViewData(): array
    {
        // Kembalikan semua sale, nanti di Blade kita filter per status
        return [
            'statuses' => Sale::with(['project', 'customer'])->whereNull('archived_at') // hanya ambil yang aktif
            ->get(),
        ];
    }


public static function getNavigationLabel(): string
{
    return 'Deals Pipeline';
}

public static function getNavigationGroup(): ?string
{
    return 'Deals'; // 👈 supaya masuk ke grup yang sama
}

public static function getNavigationIcon(): string
{
    return 'heroicon-o-squares-2x2';
}

protected function getHeaderWidgets(): array
{
    return [
         SalesTabs::class,
    ];
}



//  public function getHeader(): ?View
//     {
//         return view('filament.resources.sales.pages._sales-tabs', [
//             'active' => 'pipeline',
//         ]);
//     }



}
