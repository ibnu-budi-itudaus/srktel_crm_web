<?php

namespace App\Filament\Resources\FollowUps;

use BackedEnum;
use App\Models\FollowUp;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\FollowUps\Pages\EditFollowUp;
use App\Filament\Resources\FollowUps\Pages\ViewFollowUp;
use App\Filament\Resources\FollowUps\Pages\ListFollowUps;
use App\Filament\Resources\FollowUps\Pages\CreateFollowUp;
use App\Filament\Resources\FollowUps\Schemas\FollowUpForm;
use App\Filament\Resources\FollowUps\Tables\FollowUpsTable;

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $recordTitleAttribute = 'follow_up_date';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return FollowUpForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FollowUpsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFollowUps::route('/'),
            'create' => CreateFollowUp::route('/create'),
            'edit' => EditFollowUp::route('/{record}/edit'),
            'view' => ViewFollowUp::route('/{record}'),
        ];
    }
}
