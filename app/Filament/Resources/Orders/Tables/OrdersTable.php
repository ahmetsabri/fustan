<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->label('رقم الطلب')
                    ->toggleable(),
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('العميل')
                    ->toggleable(),
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label('المنتج')
                    ->default('غير محدد')
                    ->toggleable(),
                SelectColumn::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلق',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->toggleable()
                   ,
                TextColumn::make('tailor.name')
                    ->label('الخياط')
                    ->sortable()
                    ->default('غير معين')
                    ->toggleable(),
                TextColumn::make('total_price')
                    ->label('السعر الإجمالي')
                    ->money(fn ($record) => $record->currency ?? 'KWD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('delivery_date')
                    ->label('تاريخ التسليم')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلق',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                    ]),
                SelectFilter::make('tailor_id')
                    ->label('الخياط')
                    ->relationship('tailor', 'name', fn (Builder $query) => $query->where('role', 'tailor'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('customer_service_id')
                    ->label('خدمة العملاء')
                    ->relationship('customerService', 'name', fn (Builder $query) => $query->where('role', 'customer_service'))
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->default([
                        'created_from' => today(),
                        'created_until' => today(),
                    ])
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ')
                            ->default(today()),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ')
                            ->default(today()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->recordUrl(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('edit', ['record' => $record]))
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign_tailor')
                        ->label('تعيين خياط')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('tailor_id')
                                ->label('الخياط')
                                ->options(User::where('role', 'tailor')->where('is_active', true)->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['tailor_id' => $data['tailor_id']]);
                            });
                        }),
                    BulkAction::make('update_status')
                        ->label('تحديث الحالة')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            \Filament\Forms\Components\Select::make('status')
                                ->label('الحالة')
                                ->options([
                                    'pending' => 'معلق',
                                    'in_progress' => 'قيد التنفيذ',
                                    'completed' => 'مكتمل',
                                    'delivered' => 'تم التسليم',
                                    'cancelled' => 'ملغي',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $statusLabels = [
                                'pending' => 'معلق',
                                'in_progress' => 'قيد التنفيذ',
                                'completed' => 'مكتمل',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغي',
                            ];
                            
                            $count = $records->count();
                            $statusLabel = $statusLabels[$data['status']] ?? $data['status'];
                            
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });
                            
                            Notification::make()
                                ->title('تم تحديث الحالة بنجاح')
                                ->body("تم تحديث حالة {$count} طلب إلى: {$statusLabel}")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ]),
            ]);
    }
}
