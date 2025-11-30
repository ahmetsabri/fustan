<?php

namespace App\Filament\Widgets;

use App\Helpers\TranslationHelper;
use App\Models\Order;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    public ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Show welcome message for tailors
        if ($user->isTailor()) {
            return [
                Stat::make(
                    TranslationHelper::label('مرحباً', 'Welcome'),
                    $user->name
                )
                    ->description(TranslationHelper::label('مرحباً بك في لوحة التحكم', 'Welcome to your dashboard'))
                    ->descriptionIcon('heroicon-m-hand-raised')
                    ->color('primary')
                    ->icon('heroicon-o-user-circle')
                    ->chart([]),
            ];
        }

        $query = Order::query();

        // Filter by user role
        if ($user->isCustomerService() && !$user->isAdmin()) {
            $query->where('customer_service_id', $user->id);
        }

        // Get date filters from parent dashboard page
        $filters = $this->pageFilters ?? [];
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        // Apply date range filter
        if ($startDate) {
            $query->whereDate('delivery_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('delivery_date', '<=', $endDate);
        }

        $todayQuery = (clone $query)->whereDate('delivery_date', today());
        $weekQuery = (clone $query)->whereBetween('delivery_date', [now()->startOfWeek(), now()->endOfWeek()]);
        $monthQuery = (clone $query)->whereBetween('delivery_date', [now()->startOfMonth(), now()->endOfMonth()]);

        $totalInRange = $query->count();
        $isTailor = $user->isTailor();
      
        $stats = [
            Stat::make(TranslationHelper::label('إجمالي الطلبات', 'Total Orders'), $totalInRange)
                ->description(TranslationHelper::label('الطلبات في النطاق المحدد', 'Orders in selected range'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-chart-bar')
                ->chart([50, 100, 150, 200, $totalInRange]),
            Stat::make(TranslationHelper::label('إجمالي الطلبات (اليوم)', 'Total Orders (Today)'), $todayQuery->count())
                ->description(TranslationHelper::label('الطلبات المقرر تسليمها اليوم', 'Orders scheduled for delivery today'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-sparkles')
                ->chart([7, 12, 15, 18, $todayQuery->count()]),
            Stat::make(TranslationHelper::label('إجمالي الطلبات (هذا الأسبوع)', 'Total Orders (This Week)'), $weekQuery->count())
                ->description(TranslationHelper::label('الطلبات المقرر تسليمها هذا الأسبوع', 'Orders scheduled for delivery this week'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->icon('heroicon-o-calendar-days')
                ->chart([20, 35, 45, 55, $weekQuery->count()]),
            Stat::make(TranslationHelper::label('إجمالي الطلبات (هذا الشهر)', 'Total Orders (This Month)'), $monthQuery->count())
                ->description(TranslationHelper::label('الطلبات المقرر تسليمها هذا الشهر', 'Orders scheduled for delivery this month'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-chart-bar')
                ->chart([50, 100, 150, 200, $monthQuery->count()]),
            Stat::make(TranslationHelper::label('الطلبات المعلقة', 'Pending Orders'), (clone $query)->where('status', 'pending')->count())
                ->description(TranslationHelper::label('الطلبات في انتظار المعالجة', 'Orders awaiting processing'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->chart([5, 8, 10, 12, (clone $query)->where('status', 'pending')->count()]),
            Stat::make(TranslationHelper::label('الطلبات قيد التنفيذ', 'In Progress Orders'), (clone $query)->where('status', 'in_progress')->count())
                ->description(TranslationHelper::label('الطلبات قيد العمل', 'Orders in progress'))
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info')
                ->icon('heroicon-o-cog-6-tooth')
                ->chart([3, 6, 9, 12, (clone $query)->where('status', 'in_progress')->count()]),
            Stat::make(TranslationHelper::label('المكتملة اليوم', 'Completed Today'), (clone $query)->where('status', 'completed')->whereDate('delivery_date', today())->count())
                ->description(TranslationHelper::label('الطلبات المكتملة المقرر تسليمها اليوم', 'Completed orders scheduled for delivery today'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->chart([2, 4, 6, 8, (clone $query)->where('status', 'completed')->whereDate('delivery_date', today())->count()]),
        ];

        // Add price/currency statistics here - they will be hidden for tailors
        // Example:
        // if (!$isTailor) {
        //     $stats[] = Stat::make(...)->description('Total Revenue');
        // }

        return $stats;
    }
}
