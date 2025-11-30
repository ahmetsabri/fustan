<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Filament\Resources\Customers\CustomerResource;
use App\Helpers\TranslationHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(TranslationHelper::label('الاسم', 'Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(TranslationHelper::label('الهاتف', 'Phone'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(TranslationHelper::label('البريد الإلكتروني', 'Email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label(TranslationHelper::label('الطلبات', 'Orders'))
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-shopping-bag')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(TranslationHelper::label('تاريخ الإنشاء', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
