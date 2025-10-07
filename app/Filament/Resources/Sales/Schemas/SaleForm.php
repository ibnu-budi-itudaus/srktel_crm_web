<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
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
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->searchable()
                     ->preload()
                    ->required()
                    ->label('Project'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                // Select::make('status')
                //     ->options(['pending' => 'Pending', 'success' => 'Success', 'cancel' => 'Cancel'])
                //     ->default('pending')
                //     ->required(),

                 Select::make('status')
                    ->options(['prospect' => 'Prospect', 'deal' => 'Deal', 'no_deal' => 'No deal', 'pending' => 'Pending'])
                    ->default('prospect')
                     ->label('Status Pipeline')
                    ->native(false) // dropdown modern
                    ->required(),
            ]);
    }
}
