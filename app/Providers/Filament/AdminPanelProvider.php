<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Filament\Navigation\NavigationGroup;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     Widgets\FilamentInfoWidget::class,
            // ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                ->label('Request Services')
                ->icon('heroicon-o-rectangle-stack')
                ->collapsible(true),
                NavigationGroup::make()
                ->label('Reports')
                ->icon('heroicon-o-rectangle-stack')
                ->collapsible(true),
                NavigationGroup::make()
                ->label('User Management')
                ->collapsible(true),
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->navigationItems([
                NavigationItem::make('Generate Reports')
                ->url('https://ecg.codepps.online/reports', shouldOpenInNewTab: true)
                ->group('Reports'),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
            
            NavigationItem::make('Dashboard')
            ->url('/dashboard')
            ->icon('heroicon-o-home')
            ->activeIconColor('red') // Active color
            ->inactiveIconColor('text-gray-400') // Inactive color
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('9rem');

           
    }
}
