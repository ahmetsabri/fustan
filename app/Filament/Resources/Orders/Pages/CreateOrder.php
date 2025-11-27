<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'إضافة طلب جديد';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_service_id'] = auth()->id();

        return $data;
    }
}
