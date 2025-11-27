<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->label('من تاريخ')
                    ->default(now()->startOfMonth())
                    ->live()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidgets')),
                DatePicker::make('end_date')
                    ->label('إلى تاريخ')
                    ->default(now()->endOfMonth())
                    ->live()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidgets')),
            ]);
    }
}

