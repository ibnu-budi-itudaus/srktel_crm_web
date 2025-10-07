<?php

namespace App\Filament\Resources\FollowUps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class FollowUpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                     ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Customer'),
                DatePicker::make('follow_up_date')
                    ->label('Tanggal Follow Up')
                    ->required(),
                Textarea::make('result')
                    ->label('Hasil Follow Up')
                    ->default(null)
                    ->rows(3),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'prospect' => 'Prospect',
                        'pending' => 'Pending',
                        'deal' => 'Deal',
                        'no_deal' => 'No Deal',
                       // 'cancel' => 'Cancel',
                    ])
                    ->required()
                    ->default('prospect'),

            ]);
    }
}
