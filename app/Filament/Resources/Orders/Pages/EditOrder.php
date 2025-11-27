<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'تعديل طلب';

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Only admins and customer service can delete orders
        $user = auth()->user();
        if ($user?->isAdmin() || $user?->isCustomerService()) {
            $actions[] = DeleteAction::make();
        }

        return $actions;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        // If user is a tailor, only allow updating the status field
        if ($user?->isTailor()) {
        return [
                'status' => $data['status'] ?? $this->record->status,
        ];
        }

        return $data;
    }
}
