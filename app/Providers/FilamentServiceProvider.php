<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::registerNavigationGroups([
            __('Administrative'),
            __('Settings'),
        ]);

        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/filament.css');

            $navigationItem = [];

            if (Gate::allows('manage debug')) {
                $navigationItem[] = NavigationItem::make()
                    ->label(__('Debug'))
                    ->url(route('telescope'))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group(__('Settings'));
            }

            if (Gate::allows('manage queue')) {
                $navigationItem[] = NavigationItem::make()
                    ->label(__('Queue'))
                    ->url(route('horizon.index'))
                    ->icon('heroicon-o-view-list')
                    ->activeIcon('heroicon-s-view-list')
                    ->group(__('Settings'));
            }

            Filament::registerNavigationItems($navigationItem);
        });
    }
}
