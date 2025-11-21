<?php

declare(strict_types=1);

namespace Datlechin\FilamentMenuBuilder\Resources;

use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;


class MenuResource extends Resource
{
    public static function getModel(): string
    {
        return FilamentMenuBuilderPlugin::get()->getMenuModel();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentMenuBuilderPlugin::get()->getNavigationLabel() ?? Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    }

    public static function getNavigationIcon(): string
    {
        return FilamentMenuBuilderPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentMenuBuilderPlugin::get()->getNavigationSort();
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentMenuBuilderPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationBadge(): ?string
    {
        return FilamentMenuBuilderPlugin::get()->getNavigationCountBadge() ? number_format(static::getModel()::count()) : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('name')
                    ->label(__('filament-menu-builder::menu-builder.resource.name.label'))
                    ->required()
                    ->columnSpan(3),

                Components\ToggleButtons::make('is_visible')
                    ->grouped()
                    ->options([
                        true => __('filament-menu-builder::menu-builder.resource.is_visible.visible'),
                        false => __('filament-menu-builder::menu-builder.resource.is_visible.hidden'),
                    ])
                    ->colors([
                        true => 'primary',
                        false => 'danger',
                    ])
                    ->required()
                    ->label(__('filament-menu-builder::menu-builder.resource.is_visible.label'))
                    ->default(true),


                Section::make()
                    ->visible(fn (Component $component) => $component->evaluate(FilamentMenuBuilderPlugin::get()->getMenuFields()) !== [])
                    ->schema(FilamentMenuBuilderPlugin::get()->getMenuFields()),
            ]);
    }

    public static function table(Table $table): Table
    {
        $locations = FilamentMenuBuilderPlugin::get()->getLocations();

        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('menuItems'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('filament-menu-builder::menu-builder.resource.name.label')),
                TextColumn::make('locations.location')
                    ->label(__('filament-menu-builder::menu-builder.resource.locations.label'))
                    ->default(__('filament-menu-builder::menu-builder.resource.locations.empty'))
                    ->color(fn (string $state) => array_key_exists($state, $locations) ? 'primary' : 'gray')
                    ->formatStateUsing(fn (string $state) => $locations[$state] ?? $state)
                    ->limitList(2)
                    ->sortable()
                    ->badge(),
                TextColumn::make('menu_items_count')
                    ->label(__('filament-menu-builder::menu-builder.resource.items.label'))
                    ->icon('heroicon-o-link')
                    ->numeric()
                    ->default(0)
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label(__('filament-menu-builder::menu-builder.resource.is_visible.label'))
                    ->sortable()
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MenuResource\Pages\ListMenus::route('/'),
            'edit' => MenuResource\Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
