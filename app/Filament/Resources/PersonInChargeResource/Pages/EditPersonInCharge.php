<?php

namespace App\Filament\Resources\PersonInChargeResource\Pages;

use App\Filament\Resources\PersonInChargeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonInCharge extends EditRecord
{
    protected static string $resource = PersonInChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 