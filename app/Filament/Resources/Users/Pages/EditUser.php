<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Helpers\TranslationHelper;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return TranslationHelper::label('تعديل مستخدم', 'Edit User');
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Hide delete button if the user being edited is an admin
        if (!$this->record->isAdmin()) {
            $actions[] = DeleteAction::make();
        }

        return $actions;
    }
}
