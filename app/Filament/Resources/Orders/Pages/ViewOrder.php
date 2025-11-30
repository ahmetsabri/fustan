<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Schemas\OrderView;
use App\Helpers\CurrencyHelper;
use ArPHP\I18N\Arabic;
use Omaralalwi\Gpdf\Facade\Gpdf;
use Illuminate\Support\Facades\View;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'عرض الطلب';

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Download Invoice button
        $actions[] = Action::make('downloadInvoice')
            ->label('تحميل الفاتورة')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function () {
                $this->record->load(['customer', 'product.media', 'tailor', 'customerService']);
                
                $html = View::make('pdfs.invoice', ['order' => $this->record])->render();
                
                // Process Arabic text for proper rendering
                $arabic = new Arabic();
                $p = $arabic->arIdentify($html);
                
                for ($i = count($p) - 1; $i >= 0; $i -= 2) {
                    $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i - 1], $p[$i] - $p[$i - 1]));
                    $html = substr_replace($html, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
                }
                
                Gpdf::generateWithStream($html, 'invoice-' . $this->record->order_number . '.pdf', true);
            });

        // Hide edit button for tailors
        $user = auth()->user();
        if (!$user?->isTailor()) {
            $actions[] = EditAction::make();
        }

        return $actions;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Eager load relationships to prevent null errors
        $this->record->load(['customer', 'product', 'tailor', 'customerService', 'media']);
        
        // Add computed fields for display
        $data['customer_name'] = $this->record->customer?->name ?? 'غير محدد';
        $data['product_name'] = $this->record->product?->name ?? 'غير محدد';
        $data['tailor_name'] = $this->record->tailor?->name ?? 'غير معين';
        $data['status_display'] = match ($this->record->status) {
            'pending' => 'معلق',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            default => $this->record->status ?? 'غير محدد',
        };
        $data['total_price_display'] = $this->record->total_price 
            ? number_format($this->record->total_price, 2) . ' ' . ($this->record->currency ?? 'KWD')
            : '0.00';
        $data['currency_display'] = CurrencyHelper::getCurrencies()[$this->record->currency] ?? $this->record->currency ?? 'KWD';
        
        return $data;
    }

    public function content(Schema $schema): Schema
    {
        return OrderView::configure($schema);
    }
}
