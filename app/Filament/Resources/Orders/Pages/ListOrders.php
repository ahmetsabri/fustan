<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Helpers\TranslationHelper;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return TranslationHelper::label('قائمة الطلبات', 'Orders List');
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canCreate = $user?->isAdmin() || $user?->isCustomerService();

        return $canCreate ? [
            CreateAction::make(),
        ] : [];
    }

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        return parent::getTableQuery()
            ->when($user?->isTailor(), fn (Builder $query) => $query->where('tailor_id', $user->id))
            ->when($user?->isCustomerService() && !$user?->isAdmin(), fn (Builder $query) => 
                $query->where('customer_service_id', $user->id)
            );
    }
}
