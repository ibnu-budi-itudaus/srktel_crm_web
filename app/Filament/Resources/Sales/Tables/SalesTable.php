<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Forms;
use App\Models\Sale;
use Livewire\Livewire;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
// use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\Alignment;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Support\Colors\Color;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table

             ->query(
                Sale::query()->withCount('followUps') // 👈 ini penting
            )
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('idr')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'prospect',
                        'info'    => 'pending',
                        'success' => 'deal',
                        'danger'  => 'no_deal',
                    ])
                    ->label('Pipeline Status'),
                 // 👇 Tambahan: jumlah follow up
               BadgeColumn::make('follow_ups_count')
                ->label('Follow Ups')
                ->sortable()
                ->colors([
                    'gray' => fn ($state) => $state === 0,
                    'warning' => fn ($state) => $state > 0 && $state < 3,
                    'success' => fn ($state) => $state >= 3,
                ])
                ->formatStateUsing(fn ($state) => $state . 'x'),
                    
            ])
            ->filters([
                // Tables\Filters\Filter::make('created_today')
                //     ->label('Today')
                //     ->query(fn ($query) => $query->whereDate('created_at', today())),
                SelectFilter::make('status')
                        ->options([
                            'prospect' => 'Prospect',
                            'pending'  => 'Pending',
                            'deal'     => 'Deal',
                            'no_deal'  => 'No Deal',
                        ]),
                
                SelectFilter::make('archived_status')
            ->label('Status Arsip')
            ->options([
                'active' => 'Belum Diarsipkan',
                'archived' => 'Sudah Diarsipkan',
            ])
           // ->default('all')
            ->query(function ($query, array $data) {
                return match ($data['value'] ?? 'all') {
                    'active' => $query->whereNull('archived_at'),
                    'archived' => $query->whereNotNull('archived_at'),
                    default => $query, // "all"
                };
            }),

                    // SelectFilter::make('archived')
                    // ->label('Arsip')
                    // ->query(fn ($query) => $query->archived()),     
                
            ])

           


            ->recordActions([
               

                 // 👇 Follow Up action pindah ke sini
                Action::make('followUp')
                    ->label('Follow Up')
                    ->icon('heroicon-o-phone')
                    ->modalHeading(fn (Sale $record) => "Follow Up: {$record->project?->name} - {$record->customer?->name}")
                    ->form([
                        DatePicker::make('follow_up_date')->required(),
                        Textarea::make('result')->required()->rows(3),
                         \Filament\Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'prospect' => 'Prospect',
                                'pending'  => 'Pending',
                                'deal'     => 'Deal',
                                'no_deal'  => 'No Deal',
                            ])
                            ->required(),
                    ])

                    ->action(function (array $data, Sale $record) {
                        $record->followUps()->create([
                            'follow_up_date' => $data['follow_up_date'],
                            'result'         => $data['result'],
                            'customer_id'    => $record->customer_id,
                            'sale_id'        => $record->id,
                            'status'         => $data['status'],
                        ]);

                            // ✅ Update status sale langsung
                        $record->update(['status' => $data['status']]);

                        Notification::make()
                            ->title('Follow Up berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),

                  Action::make('timeline')
                ->label('Timeline')
                ->icon('heroicon-o-clock')
                ->modalHeading(fn ($record) => "Timeline Follow Up: {$record->project?->name} - {$record->customer?->name}")
                ->modalContent(fn ($record) => 
                    view('filament.resources.sales.pages.timeline-modal', [
                        'saleId' => $record->id,
                    ])
                )
    
            ->modalWidth('lg')
            ->modalSubmitAction(false)
            ->modalCancelAction(false),

                                    

                     ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('toggleArchive')
                    ->label(fn (Sale $record) => $record->archived_at !== null ? 'Pulihkan' : 'Arsipkan')
                     ->icon(fn (Sale $record) => $record->archived_at ? 'heroicon-o-arrow-path' : 'heroicon-o-archive-box')
                     ->color(fn (Sale $record) => $record->archived_at ? 'success' : 'danger')
                     ->requiresConfirmation()  
                      ->successNotificationTitle(fn (Sale $record) => $record->archived_at ? 'Berhasil diarsipkan ke Riwayat Sale' : 'Berhasil dipulihkan') 
                     ->visible(fn (Sale $record) => in_array($record->status, ['deal', 'no_deal'])) // 🔑 hanya deal / no_deal
                     ->action(function (Sale $record) {
                        if ($record->archived_at) {
                            $record->restore();
                        } else {
                            $record->archive();
                        }
                     }),
                    // ->visible(fn ($record) => $record->archived_at !== null),
                    ],position: RecordActionsPosition::AfterColumns)
                    
                     ->recordActionsAlignment('start')
           
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                     BulkAction::make('archive')
            ->label('Arsipkan')
            ->icon('heroicon-m-archive-box')
            ->color(Color::Amber)
            ->requiresConfirmation()
            ->successNotificationTitle('Berhasil diarsipkan ke Riwayat Sale') 
            ->action(function ($records) {
                foreach ($records as $record) {
                    $record->archive();
                }
            }),

        BulkAction::make('toggleArchive')
            ->label('Kembalikan')
            ->icon('heroicon-m-arrow-path')
            ->color(Color::Emerald)
            ->requiresConfirmation()
            ->successNotificationTitle('Berhasil dipulihkan') 
            ->action(function ($records) {
                foreach ($records as $record) {
                    $record->restore();
                }
            }),
                    
                ]),
            ]);
    }

    
}
