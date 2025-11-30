<?php

namespace App\Filament\Pages;

use App\Helpers\TranslationHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        // Hide date filters for tailors
        if (Auth::user()?->isTailor() ?? false) {
            return $schema->components([]);
        }

        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->label(TranslationHelper::label('من تاريخ', 'From Date'))
                    ->default(now()->startOfMonth())
                    ->live()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidgets')),
                DatePicker::make('end_date')
                    ->label(TranslationHelper::label('إلى تاريخ', 'To Date'))
                    ->default(now()->endOfMonth())
                    ->live()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidgets')),
            ]);
    }
}

