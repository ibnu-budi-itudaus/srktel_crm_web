<?php

namespace App\Filament\Resources\FollowUps\Pages;

use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\FollowUps\FollowUpResource;

class ViewFollowUp extends ViewRecord
{
    protected static string $resource = FollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

             Actions\Action::make('back')
                ->label('Back')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => route('filament.admin.resources.follow-ups.index')),
        ];
    }
}
