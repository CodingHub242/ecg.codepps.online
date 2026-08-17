<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use \App\Models\User;

class UsersChart extends ChartWidget
{
    protected static ?string $heading = 'Registered Users In This Year';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $roles = auth()->user()->roles->pluck('name')->toArray();
        if (in_array('super_admin', $roles)) {
            return true;
        } else {
            return false;
        }
    }

    protected function getData(): array
    {
        $data = Trend::model(User::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        return [
            'datasets' => [
            [
                'label' => 'Users',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
