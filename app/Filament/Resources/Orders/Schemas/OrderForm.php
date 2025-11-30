<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Helpers\CurrencyHelper;
use App\Helpers\TranslationHelper;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        // Helper closure to disable fields if not admin and not the creator
        $shouldDisable = function ($record) {
            $user = Auth::user();
            // Disable if not admin AND not the creator
            return $user?->isTailor();
        };

        return $schema
            ->components([
                // Customer Section
                Select::make('customer_id')
                    ->label(TranslationHelper::label('العميل', 'Customer'))
                    ->relationship('customer', 'name', fn (Builder $query) => $query->orderBy('name'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => 
                        \App\Models\Customer::query()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($customer) => [$customer->getKey() => $customer->name])
                            ->toArray()
                    )
                    ->preload()
                    ->required()
                    ->disabled($shouldDisable)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(TranslationHelper::label('الاسم', 'Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(TranslationHelper::label('الهاتف', 'Phone'))
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(TranslationHelper::label('البريد الإلكتروني', 'Email'))
                            ->email()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label(TranslationHelper::label('العنوان', 'Address'))
                            ->rows(2),
                        Textarea::make('notes')
                            ->label(TranslationHelper::label('ملاحظات', 'Notes'))
                            ->rows(2),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Customer::create($data)->id;
                    }),

                // Product Section
                Select::make('product_id')
                    ->label(TranslationHelper::label('المنتج', 'Product'))
                    ->relationship('product', 'name', fn (Builder $query) => $query->where('is_active', true)->orderBy('name'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled($shouldDisable)
                    ->live()
                    ->afterStateUpdated(fn ($state, $set) => $set('product_images', null)),

                // Product Images Display (for tailors only)
                ViewField::make('product_images')
                    ->label(TranslationHelper::label('صور المنتج', 'Product Images'))
                    ->view('filament.forms.components.product-images')
                    ->visible(function ($get) {
                        $user = Auth::user();
                        return $user?->isTailor();
                    })
                    ->viewData(function ($get, $record) {
                        $productId =  $record?->product_id;
                        if (!$productId) {
                            return ['images' => collect([])];
                        }
                        $product = Product::with('media')->find($productId);
                        return [
                            'images' => $product?->media('images')->get() ?? collect([]),
                        ];
                    })
                    ->columnSpanFull(),

                // Measurements Section
                Grid::make(3)
                ->columnSpanFull()
                    ->schema([
                        TextInput::make('length')
                            ->label(TranslationHelper::label('الطول', 'Length'))
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('shoulder')
                            ->label(TranslationHelper::label('الكتف', 'Shoulder'))
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('chest')
                            ->label(TranslationHelper::label('الصدر', 'Chest'))
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('waist')
                            ->label(TranslationHelper::label('الخصر', 'Waist'))
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('hip')
                            ->label(TranslationHelper::label('الورك', 'Hip'))
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('sleeve')
                            ->label(TranslationHelper::label('الكُم', 'Sleeve'))
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                    ]),

                Textarea::make('measurement_notes')
                    ->label(TranslationHelper::label('ملاحظات المقاسات', 'Measurement Notes'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled($shouldDisable),

                // Order Details Section
                Grid::make(2)
                ->columnSpanFull()
                    ->schema([
                        Select::make('tailor_id')
                            ->label(TranslationHelper::label('الخياط', 'Tailor'))
                            ->relationship('tailor', 'name', fn (Builder $query) => $query->where('role', 'tailor')->where('is_active', true)->orderBy('name'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled($shouldDisable)
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if ($value) {
                                            $user = User::find($value);
                                            if (!$user || $user->role !== 'tailor') {
                                                $fail(TranslationHelper::label('يجب أن يكون الخياط المحدد لديه دور الخياط.', 'The selected tailor must have the tailor role.'));
                                            }
                                        }
                                    };
                                },
                            ]),
                        Select::make('status')
                            ->label(TranslationHelper::label('الحالة', 'Status'))
                            ->options(TranslationHelper::options([
                                'pending' => ['ar' => 'معلق', 'en' => 'Pending'],
                                'in_progress' => ['ar' => 'قيد التنفيذ', 'en' => 'In Progress'],
                                'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
                                'delivered' => ['ar' => 'تم التسليم', 'en' => 'Delivered'],
                                'cancelled' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
                            ]))
                            ->required()
                            ->default('pending'),
                        TextInput::make('total_price')
                            ->label(TranslationHelper::label('السعر الإجمالي', 'Total Price'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->disabled($shouldDisable),
                        Select::make('currency')
                            ->label(TranslationHelper::label('العملة', 'Currency'))
                            ->options(CurrencyHelper::getCurrencies())
                            ->required()
                            ->default(CurrencyHelper::getDefaultCurrency())
                            ->searchable()
                            ->disabled($shouldDisable),
                        DatePicker::make('delivery_date')
                        ->columnSpanFull()
                            ->label(TranslationHelper::label('تاريخ التسليم', 'Delivery Date'))
                            ->nullable()
                            ->disabled($shouldDisable),
                    ]),

                Textarea::make('notes')
                    ->label(TranslationHelper::label('ملاحظات', 'Notes'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled($shouldDisable),

                // Order Attachments
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('attachments')
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->reorderable()
                    ->downloadable()
                    ->openable()
                    ->label(TranslationHelper::label('مرفقات الطلب', 'Order Attachments'))
                    ->columnSpanFull()
                    ->disabled($shouldDisable),
            ]);
    }
}
