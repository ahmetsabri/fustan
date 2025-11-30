<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Helpers\CurrencyHelper;
use App\Helpers\TranslationHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(TranslationHelper::label('الاسم', 'Name'))
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(TranslationHelper::label('الوصف', 'Description'))
                    ->rows(3)
                    ->columnSpanFull(),
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('price')
                            ->label(TranslationHelper::label('السعر', 'Price'))
                            ->numeric()
                            ->required()
                            ->step(0.01),
                        Select::make('currency')
                            ->label(TranslationHelper::label('العملة', 'Currency'))
                            ->options(CurrencyHelper::getCurrencies())
                            ->required()
                            ->default(CurrencyHelper::getDefaultCurrency())
                            ->searchable(),
                    ]),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->reorderable()
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull()
                    ->label(TranslationHelper::label('صور المنتج', 'Product Images')),
                Toggle::make('is_active')
                    ->default(true)
                    ->label(TranslationHelper::label('نشط', 'Active'))
                    ->columnSpanFull(),
            ]);
    }
}
