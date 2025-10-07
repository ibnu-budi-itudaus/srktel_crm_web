<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Proyek')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Misal: CCTV, Alarm, Kabel FO'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->default(null)
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Tuliskan detail tentang proyek'),
            ]);
    }
}
