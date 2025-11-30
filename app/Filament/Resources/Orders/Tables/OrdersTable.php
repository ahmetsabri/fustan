<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Helpers\TranslationHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PDF;

use Filament\Actions\Action;
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
use Illuminate\Support\HtmlString;
use ArPHP\I18N\Arabic;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['statusAudits.user']))
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->label(TranslationHelper::label('رقم الطلب', 'Order Number'))
                    ->toggleable(),
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label(TranslationHelper::label('العميل', 'Customer'))
                    ->toggleable(),
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label(TranslationHelper::label('المنتج', 'Product'))
                    ->default(TranslationHelper::label('غير محدد', 'Not Specified'))
                    ->toggleable(),
                SelectColumn::make('status')
                    ->label(TranslationHelper::label('الحالة', 'Status'))
                    ->options(TranslationHelper::options([
                        'pending' => ['ar' => 'معلق', 'en' => 'Pending'],
                        'in_progress' => ['ar' => 'قيد التنفيذ', 'en' => 'In Progress'],
                        'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
                        'delivered' => ['ar' => 'تم التسليم', 'en' => 'Delivered'],
                        'cancelled' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
                    ]))
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->toggleable()
                    ->tooltip(function ($record) {
                        $statusLabels = [
                            'pending' => TranslationHelper::label('معلق', 'Pending'),
                            'in_progress' => TranslationHelper::label('قيد التنفيذ', 'In Progress'),
                            'completed' => TranslationHelper::label('مكتمل', 'Completed'),
                            'delivered' => TranslationHelper::label('تم التسليم', 'Delivered'),
                            'cancelled' => TranslationHelper::label('ملغي', 'Cancelled'),
                        ];
                        
                        $audits = $record->statusAudits->sortByDesc('created_at');
                        
                        if ($audits->isEmpty()) {
                            return TranslationHelper::label('لا يوجد تاريخ للحالة', 'No status history');
                        }
                        
                        $history = [];
                        foreach ($audits->reverse() as $audit) {
                            $statusLabel = $statusLabels[$audit->status] ?? $audit->status;
                            $fromStatusLabel = $audit->from_status ? ($statusLabels[$audit->from_status] ?? $audit->from_status) : null;
                            $userName = $audit->user?->name ?? TranslationHelper::label('غير معروف', 'Unknown');
                            $date = $audit->created_at->format('Y-m-d H:i');
                            
                            if ($fromStatusLabel) {
                                $history[] = "{$fromStatusLabel} -> {$statusLabel} ({$userName}) - {$date}";
                            } else {
                                $history[] = "{$statusLabel} ({$userName}) - {$date}";
                            }
                        }
                        
                        return new HtmlString(TranslationHelper::label('تاريخ الحالة:', 'Status History:') . '<br>' . implode('<br>', $history));
                    }),
                TextColumn::make('tailor.name')
                    ->label(TranslationHelper::label('الخياط', 'Tailor'))
                    ->sortable()
                    ->default(TranslationHelper::label('غير معين', 'Not Assigned'))
                    ->toggleable(),
                TextColumn::make('customerService.name')
                    ->label(TranslationHelper::label('تم الإنشاء بواسطة', 'Created By'))
                    ->sortable()
                    ->searchable()
                    ->default(TranslationHelper::label('غير محدد', 'Not Specified'))
                    ->toggleable(),
                TextColumn::make('total_price')
                    ->label(TranslationHelper::label('السعر الإجمالي', 'Total Price'))
                    ->money(fn ($record) => $record->currency ?? 'KWD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold')
                    ->toggleable()
                    ->hidden(fn () => Auth::user()?->isTailor() ?? false),
                TextColumn::make('delivery_date')
                    ->label(TranslationHelper::label('تاريخ التسليم', 'Delivery Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(TranslationHelper::label('تاريخ الإنشاء', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(TranslationHelper::label('الحالة', 'Status'))
                    ->options(TranslationHelper::options([
                        'pending' => ['ar' => 'معلق', 'en' => 'Pending'],
                        'in_progress' => ['ar' => 'قيد التنفيذ', 'en' => 'In Progress'],
                        'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
                        'delivered' => ['ar' => 'تم التسليم', 'en' => 'Delivered'],
                        'cancelled' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
                    ])),
                SelectFilter::make('tailor_id')
                    ->label(TranslationHelper::label('الخياط', 'Tailor'))
                    ->relationship('tailor', 'name', fn (Builder $query) => $query->where('role', 'tailor'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('customer_service_id')
                    ->label(TranslationHelper::label('خدمة العملاء', 'Customer Service'))
                    ->relationship('customerService', 'name', fn (Builder $query) => $query->where('role', 'customer_service'))
                    ->searchable()
                    ->preload(),
                Filter::make('delivery_date')
                    ->label(TranslationHelper::label('تاريخ التسليم', 'Delivery Date'))
                    ->default([
                        'delivery_from' => today(),
                        'delivery_until' => today(),
                    ])
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('delivery_from')
                            ->label(TranslationHelper::label('من تاريخ', 'From Date'))
                            ->default(today())
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('delivery_until')
                            ->label(TranslationHelper::label('إلى تاريخ', 'To Date'))
                            ->default(today())
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['delivery_from']) && $data['delivery_from']) {
                            $query->whereDate('delivery_date', '>=', $data['delivery_from']);
                        }
                        
                        if (isset($data['delivery_until']) && $data['delivery_until']) {
                            $query->whereDate('delivery_date', '<=', $data['delivery_until']);
                        }
                        
                        return $query;
                    }),
            ])
            ->recordActions([
                Action::make('downloadInvoice')
                    ->label(TranslationHelper::label('تحميل الفاتورة', 'Download Invoice'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->hidden(fn () => Auth::user()?->isTailor() ?? false)
                    ->action(function ($record) {
                        $record->load(['customer', 'product.media', 'tailor', 'customerService']);
                        $reportHtml = view('pdfs.invoice', ['order' => $record])->render();
        
                        $arabic = new Arabic();
                        $p = $arabic->arIdentify($reportHtml);
                
                        for ($i = count($p)-1; $i >= 0; $i-=2) {
                            $utf8ar = $arabic->utf8Glyphs(substr($reportHtml, $p[$i-1], $p[$i] - $p[$i-1]));
                            $reportHtml = substr_replace($reportHtml, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
                        }
                
                        $pdf = PDF::loadHTML($reportHtml);
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                            }, 'invoice-' . $record->order_number . '.pdf');



                        
                    }),
                EditAction::make(),
            ])
            ->recordUrl(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('edit', ['record' => $record]))
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign_tailor')
                        ->label(TranslationHelper::label('تعيين خياط', 'Assign Tailor'))
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('tailor_id')
                                ->label(TranslationHelper::label('الخياط', 'Tailor'))
                                ->options(User::where('role', 'tailor')->where('is_active', true)->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['tailor_id' => $data['tailor_id']]);
                            });
                        }),
                    BulkAction::make('update_status')
                        ->label(TranslationHelper::label('تحديث الحالة', 'Update Status'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            \Filament\Forms\Components\Select::make('status')
                                ->label(TranslationHelper::label('الحالة', 'Status'))
                                ->options(TranslationHelper::options([
                                    'pending' => ['ar' => 'معلق', 'en' => 'Pending'],
                                    'in_progress' => ['ar' => 'قيد التنفيذ', 'en' => 'In Progress'],
                                    'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
                                    'delivered' => ['ar' => 'تم التسليم', 'en' => 'Delivered'],
                                    'cancelled' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
                                ]))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $statusLabels = [
                                'pending' => TranslationHelper::label('معلق', 'Pending'),
                                'in_progress' => TranslationHelper::label('قيد التنفيذ', 'In Progress'),
                                'completed' => TranslationHelper::label('مكتمل', 'Completed'),
                                'delivered' => TranslationHelper::label('تم التسليم', 'Delivered'),
                                'cancelled' => TranslationHelper::label('ملغي', 'Cancelled'),
                            ];
                            
                            $count = $records->count();
                            $statusLabel = $statusLabels[$data['status']] ?? $data['status'];
                            
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });
                            
                            Notification::make()
                                ->title(TranslationHelper::label('تم تحديث الحالة بنجاح', 'Status updated successfully'))
                                ->body(TranslationHelper::label("تم تحديث حالة {$count} طلب إلى: {$statusLabel}", "Updated {$count} order(s) status to: {$statusLabel}"))
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
