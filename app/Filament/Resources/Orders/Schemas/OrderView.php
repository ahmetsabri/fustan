<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Helpers\CurrencyHelper;
use App\Helpers\TranslationHelper;
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
                    ->label(TranslationHelper::label('رقم الطلب', 'Order Number'))
                    ->disabled(),

                // Customer Section
                TextInput::make('customer_name')
                    ->label(TranslationHelper::label('العميل', 'Customer'))
                    ->disabled()
                    ->dehydrated(false),

                // Product Section
                TextInput::make('product_name')
                    ->label(TranslationHelper::label('المنتج', 'Product'))
                    ->disabled()
                    ->dehydrated(false),

                // Measurements Section
                Grid::make(3)
                    ->schema([
                        TextInput::make('length')
                            ->label(TranslationHelper::label('الطول', 'Length'))
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('shoulder')
                            ->label(TranslationHelper::label('الكتف', 'Shoulder'))
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('chest')
                            ->label(TranslationHelper::label('الصدر', 'Chest'))
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('waist')
                            ->label(TranslationHelper::label('الخصر', 'Waist'))
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('hip')
                            ->label(TranslationHelper::label('الورك', 'Hip'))
                            ->suffix('cm')
                            ->disabled(),
                        TextInput::make('sleeve')
                            ->label(TranslationHelper::label('الكُم', 'Sleeve'))
                            ->suffix('cm')
                            ->disabled(),
                    ]),

                Textarea::make('measurement_notes')
                    ->label(TranslationHelper::label('ملاحظات المقاسات', 'Measurement Notes'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled(),

                // Order Details Section
                Grid::make(2)
                    ->schema([
                        TextInput::make('tailor_name')
                            ->label(TranslationHelper::label('الخياط', 'Tailor'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('status_display')
                            ->label(TranslationHelper::label('الحالة', 'Status'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('total_price_display')
                            ->label(TranslationHelper::label('السعر الإجمالي', 'Total Price'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('currency_display')
                            ->label(TranslationHelper::label('العملة', 'Currency'))
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('delivery_date')
                            ->label(TranslationHelper::label('تاريخ التسليم', 'Delivery Date'))
                            ->disabled()
                            ->nullable(),
                    ]),

                Textarea::make('notes')
                    ->label(TranslationHelper::label('ملاحظات', 'Notes'))
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
                    ->label(TranslationHelper::label('مرفقات الطلب', 'Order Attachments')),
            ]);
    }
}

