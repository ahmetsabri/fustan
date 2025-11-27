<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Helpers\CurrencyHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderView
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Order Number
                TextInput::make('order_number')
                    ->label('رقم الطلب')
                    ->disabled(),

                // Customer Section
                TextInput::make('customer_name')
                    ->label('العميل')
                    ->disabled()
                    ->dehydrated(false),

                // Product Section
                TextInput::make('product_name')
                    ->label('المنتج')
                    ->disabled()
                    ->dehydrated(false),

                // Measurements Section
                Grid::make(3)
                    ->schema([
                        TextInput::make('length')
                            ->label('الطول')
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('shoulder')
                            ->label('الكتف')
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('chest')
                            ->label('الصدر')
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('waist')
                            ->label('الخصر')
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('hip')
                            ->label('الورك')
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('sleeve')
                            ->label('الكُم')
                            ->suffix('cm')
                            ->disabled(),
                    ]),

                Textarea::make('measurement_notes')
                    ->label('ملاحظات_المقاسات')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled(),

                // Order Details Section
                Grid::make(2)
                    ->schema([
                        TextInput::make('tailor_name')
                            ->label('الخياط')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('status_display')
                            ->label('الحالة')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('total_price_display')
                            ->label('السعر الإجمالي')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('currency_display')
                            ->label('العملة')
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('delivery_date')
                            ->label('تاريخ التسليم')
                            ->disabled()
                            ->nullable(),
                    ]),

                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled(),

                // Order Attachments - Read-only view
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('attachments')
                    ->multiple()
                    ->image()
                    ->downloadable()
                    ->openable()
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull()
                    ->label('مرفقات الطلب'),
            ]);
    }
}

