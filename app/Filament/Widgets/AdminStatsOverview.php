<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\User;
use App\Models\Theft;
use App\Models\Fault;
use App\Models\ElectricityRequest;


class AdminStatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        $roles = auth()->user()->roles->pluck('name')->toArray();
        if (in_array('super_admin', $roles)) {
            return true;
        } else {
            return false;
        }
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::query()->count())
                ->description('All users')
                ->descriptionIcon('heroicon-o-user-group')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "userSection()",
                ]),

            Stat::make('Theft Reports', Theft::query()->count())
                ->description('All Theft Reports')
                ->descriptionIcon('heroicon-o-bolt')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "eventSection()",
                ]),
                
            Stat::make('Faults Reported', Fault::query()->count())
                ->description('All Faults')
                ->descriptionIcon('heroicon-o-document')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "venueSection()",
                ]),

            Stat::make('Electricity Requests', ElectricityRequest::query()->count())
                ->description('All Requests')
                ->descriptionIcon('heroicon-o-document')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    //'wire:click' => "venueSection()",
                ]),

            // Stat::make('Services', Service::query()->count())
            //     ->description('All')
            //     ->descriptionIcon('heroicon-o-globe-alt')
            //     ->extraAttributes([
            //         'class' => 'cursor-pointer',
            //         'wire:click' => "tourSection()",
            //     ]),

            // Stat::make('Orders', Order::query()->count())
            //     ->description('All orders')
            //     ->descriptionIcon('heroicon-o-truck')
            //     ->extraAttributes([
            //         'class' => 'cursor-pointer',
            //         'wire:click' => "orderSection()",
            //     ]),
            
        ];
    }

    public function eventSection()
    {
        return redirect()->to('/admin/thefts');
    }
    public function userSection()
    {
        //return redirect()->to('/admin/users');
    }
    public function tourSection()
    {
       // return redirect()->to('/admin/tours');
    }
    public function venueSection()
    {
       return redirect()->to('/admin/faults');
    }

    public function orderSection()
    {
       // return redirect()->to('/admin/orders');
    }
}
