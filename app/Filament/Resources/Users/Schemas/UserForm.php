<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Helpers\TranslationHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(TranslationHelper::label('الاسم', 'Name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(TranslationHelper::label('البريد الإلكتروني', 'Email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(TranslationHelper::label('الهاتف', 'Phone'))
                    ->maxLength(255),
                Select::make('role')
                    ->label(TranslationHelper::label('الدور', 'Role'))
                    ->options(TranslationHelper::options([
                        'admin' => ['ar' => 'مدير', 'en' => 'Admin'],
                        'customer_service' => ['ar' => 'خدمة العملاء', 'en' => 'Customer Service'],
                        'tailor' => ['ar' => 'خياط', 'en' => 'Tailor'],
                    ]))
                    ->required()
                    ->default('customer_service'),
                TextInput::make('password')
                    ->label(TranslationHelper::label('كلمة المرور', 'Password'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true)
                    ->label(TranslationHelper::label('نشط', 'Active'))
                    ->columnSpanFull(),
            ]);
    }
}
