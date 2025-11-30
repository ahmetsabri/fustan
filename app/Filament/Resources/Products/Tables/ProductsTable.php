<?php

namespace App\Filament\Resources\Products\Tables;

use App\Helpers\TranslationHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('media'))
            ->columns([
                SpatieMediaLibraryImageColumn::make('primary_image')
                    ->collection('images')
                    ->label(TranslationHelper::label('الصورة', 'Image'))
                    ->size( 60)
                    ->extraAttributes(['class' => 'rounded-lg']),
                TextColumn::make('name')
                    ->label(TranslationHelper::label('الاسم', 'Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(TranslationHelper::label('السعر', 'Price'))
                    ->money(fn ($record) => $record->currency ?? 'KWD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(TranslationHelper::label('نشط', 'Active'))
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(TranslationHelper::label('تاريخ الإنشاء', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(TranslationHelper::label('نشط', 'Active'))
                    ->options(TranslationHelper::options([
                        1 => ['ar' => 'نشط', 'en' => 'Active'],
                        0 => ['ar' => 'غير نشط', 'en' => 'Inactive'],
                    ])),
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
}
