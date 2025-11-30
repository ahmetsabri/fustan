<?php

namespace App\Filament\Resources\Users\Tables;

use App\Helpers\TranslationHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(TranslationHelper::label('الاسم', 'Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(TranslationHelper::label('البريد الإلكتروني', 'Email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(TranslationHelper::label('الهاتف', 'Phone'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label(TranslationHelper::label('الدور', 'Role'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => TranslationHelper::label('مدير', 'Admin'),
                        'customer_service' => TranslationHelper::label('خدمة العملاء', 'Customer Service'),
                        'tailor' => TranslationHelper::label('خياط', 'Tailor'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'customer_service' => 'info',
                        'tailor' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-o-shield-check',
                        'customer_service' => 'heroicon-o-user-group',
                        'tailor' => 'heroicon-o-scissors',
                        default => 'heroicon-o-user',
                    })
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(TranslationHelper::label('نشط', 'Active'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(TranslationHelper::label('تاريخ الإنشاء', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(TranslationHelper::label('الدور', 'Role'))
                    ->options(TranslationHelper::options([
                        'admin' => ['ar' => 'مدير', 'en' => 'Admin'],
                        'customer_service' => ['ar' => 'خدمة العملاء', 'en' => 'Customer Service'],
                        'tailor' => ['ar' => 'خياط', 'en' => 'Tailor'],
                    ])),
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
