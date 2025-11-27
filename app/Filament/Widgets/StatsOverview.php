<?php

namespace App\Filament\Widgets;

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
        $query = Order::query();

        // Filter by user role
        if ($user->isTailor()) {
            $query->where('tailor_id', $user->id);
        } elseif ($user->isCustomerService() && !$user->isAdmin()) {
            $query->where('customer_service_id', $user->id);
        }

        // Get date filters from parent dashboard page
        $filters = $this->pageFilters ?? [];
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        // Apply date range filter
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $todayQuery = (clone $query)->whereDate('created_at', today());
        $weekQuery = (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        $monthQuery = (clone $query)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);

        $totalInRange = $query->count();

        return [
            Stat::make('إجمالي الطلبات', $totalInRange)
                ->description('الطلبات في النطاق المحدد')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-chart-bar')
                ->chart([50, 100, 150, 200, $totalInRange]),
            Stat::make('إجمالي الطلبات (اليوم)', $todayQuery->count())
                ->description('الطلبات المُنشأة اليوم')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-sparkles')
                ->chart([7, 12, 15, 18, $todayQuery->count()]),
            Stat::make('إجمالي الطلبات (هذا الأسبوع)', $weekQuery->count())
                ->description('الطلبات المُنشأة هذا الأسبوع')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->icon('heroicon-o-calendar-days')
                ->chart([20, 35, 45, 55, $weekQuery->count()]),
            Stat::make('إجمالي الطلبات (هذا الشهر)', $monthQuery->count())
                ->description('الطلبات المُنشأة هذا الشهر')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-chart-bar')
                ->chart([50, 100, 150, 200, $monthQuery->count()]),
            Stat::make('الطلبات المعلقة', (clone $query)->where('status', 'pending')->count())
                ->description('الطلبات في انتظار المعالجة')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->chart([5, 8, 10, 12, (clone $query)->where('status', 'pending')->count()]),
            Stat::make('الطلبات قيد التنفيذ', (clone $query)->where('status', 'in_progress')->count())
                ->description('الطلبات قيد العمل')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info')
                ->icon('heroicon-o-cog-6-tooth')
                ->chart([3, 6, 9, 12, (clone $query)->where('status', 'in_progress')->count()]),
            Stat::make('المكتملة اليوم', (clone $query)->where('status', 'completed')->whereDate('created_at', today())->count())
                ->description('الطلبات المكتملة اليوم')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->chart([2, 4, 6, 8, (clone $query)->where('status', 'completed')->whereDate('created_at', today())->count()]),
        ];
    }
}
