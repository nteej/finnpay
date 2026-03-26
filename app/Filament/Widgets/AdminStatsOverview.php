<?php

namespace App\Filament\Widgets;

use App\Models\Release;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingVerification = User::where('is_verified', false)->where('is_admin', false)->count();
        $totalFreelancers    = User::where('is_admin', false)->count();
        $clearedLkr          = Transaction::where('status', 'cleared')->sum('final_lkr');
        $releasedLkr         = Release::where('status', 'completed')->sum('total_lkr');
        $totalTxns           = Transaction::count();

        return [
            Stat::make('Pending Verification', $pendingVerification)
                ->description('Freelancers awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingVerification > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.users.index', ['activeTab' => 'pending'])),

            Stat::make('Total Freelancers', $totalFreelancers)
                ->description('Registered accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Cleared Balance', 'LKR ' . number_format($clearedLkr, 2))
                ->description($totalTxns . ' total transactions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make('Total Released', 'LKR ' . number_format($releasedLkr, 2))
                ->description('Paid out to bank accounts')
                ->descriptionIcon('heroicon-m-arrow-down-on-square')
                ->color('success'),
        ];
    }
}
