<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('company_name')
                ->label('Company Name')
                ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->default(null)
                     ->required()
                    ->unique(ignorable: fn ($record) => $record), // <= mencegah duplikat saat create/update,
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null)
                    ->required()
                     ->unique(ignorable: fn ($record) => $record), // <= mencegah duplikat saat create/update,
                TextInput::make('address')
                    ->default(null),
                Select::make('source_id')
                    ->label('Source')
                    ->relationship('source', 'name') 
                    ->required()
                    ->searchable()
                    ->preload(),
                // Select::make('status')
                //     ->options(['prospect' => 'Prospect', 'deal' => 'Deal', 'no_deal' => 'No deal', 'pending' => 'Pending'])
                // //     ->default('prospect')
                //     ->required(),
            ]);
    }
}
