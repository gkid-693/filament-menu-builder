<?php

declare(strict_types=1);

namespace Datlechin\FilamentMenuBuilder\Resources\MenuResource\Pages;

use Datlechin\FilamentMenuBuilder\Concerns\HasLocationAction;
use Datlechin\FilamentMenuBuilder\Resources\MenuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMenu extends EditRecord
{
    use HasLocationAction;

    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            $this->getLocationAction(),
        ];
    }
}
