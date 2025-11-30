<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Helpers\TranslationHelper;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                ->columnSpanFull()
                    ->label(TranslationHelper::label('البريد الإلكتروني', 'Email'))
                    ->email()
                    ->maxLength(255),
                Textarea::make('address')
                    ->label(TranslationHelper::label('العنوان', 'Address'))
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(TranslationHelper::label('ملاحظات', 'Notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
