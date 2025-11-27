<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Helpers\CurrencyHelper;
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
                    ->label('العميل')
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
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->rows(2),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Customer::create($data)->id;
                    }),

                // Product Section
                Select::make('product_id')
                    ->label('المنتج')
                    ->relationship('product', 'name', fn (Builder $query) => $query->where('is_active', true)->orderBy('name'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->disabled($shouldDisable)
                    ->live()
                    ->afterStateUpdated(fn ($state, $set) => $set('product_images', null)),

                // Product Images Display (for tailors only)
                ViewField::make('product_images')
                    ->label('صور المنتج')
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
                            ->label('الطول')
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('shoulder')
                            ->label('الكتف')
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('chest')
                            ->label('الصدر')
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('waist')
                            ->label('الخصر')
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('hip')
                            ->label('الورك')
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                        TextInput::make('sleeve')
                            ->label('الكُم')
                            ->suffix('cm')
                            ->rules(['nullable', 'regex:/^\d+(\.\d+)?$/'])
                            ->disabled($shouldDisable),
                    ]),

                Textarea::make('measurement_notes')
                    ->label('ملاحظات_المقاسات')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled($shouldDisable),

                // Order Details Section
                Grid::make(2)
                ->columnSpanFull()
                    ->schema([
                        Select::make('tailor_id')
                            ->label('الخياط')
                            ->relationship('tailor', 'name', fn (Builder $query) => $query->where('role', 'tailor')->where('is_active', true)->orderBy('name'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->disabled($shouldDisable)
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if ($value) {
                                            $user = \App\Models\User::find($value);
                                            if (!$user || $user->role !== 'tailor') {
                                                $fail('يجب أن يكون الخياط المحدد لديه دور الخياط.');
                                            }
                                        }
                                    };
                                },
                            ]),
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'معلق',
                                'in_progress' => 'قيد التنفيذ',
                                'completed' => 'مكتمل',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغي',
                            ])
                            ->required()
                            ->default('pending'),
                        TextInput::make('total_price')
                            ->label('السعر الإجمالي')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->disabled($shouldDisable),
                        Select::make('currency')
                            ->label('العملة')
                            ->options(CurrencyHelper::getCurrencies())
                            ->required()
                            ->default(CurrencyHelper::getDefaultCurrency())
                            ->searchable()
                            ->disabled($shouldDisable),
                        DatePicker::make('delivery_date')
                        ->columnSpanFull()
                            ->label('تاريخ التسليم')
                            ->nullable()
                            ->disabled($shouldDisable),
                    ]),

                Textarea::make('notes')
                    ->label('ملاحظات')
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
                    ->label('مرفقات الطلب')
                    ->columnSpanFull()
                    ->disabled($shouldDisable),
            ]);
    }
}
