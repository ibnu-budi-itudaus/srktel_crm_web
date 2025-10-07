<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\FollowUpChart;
use App\Filament\Widgets\ForecastStats;
use App\Filament\Widgets\StatsOverview;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                StatsOverview::class,
                SalesChart::class, 
                FollowUpChart::class,
            ])

                ->navigationGroups([
                NavigationGroup::make()
                    ->label('Sales')
                    ->icon('heroicon-o-document-check')
                    ->collapsed(),
            ])


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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
            'panels::head.end',
            fn () => '<style>
                /* Fix Filament tabs alignment and spacing */
                .filament-tabs {
                    justify-content: flex-start !important;
                    width: 100% !important;
                }
                
                .filament-tabs .filament-tabs-list {
                    justify-content: flex-start !important;
                    width: 100% !important;
                }
                
                .filament-tabs-item {
                    justify-content: flex-start !important;
                    text-align: left !important;
                    padding: 0.75rem 1rem !important;
                    margin: 0 !important;
                }
                
                /* Fix active state */
                .filament-tabs-item.active {
                    color: #3b82f6 !important;
                    border-bottom-color: #3b82f6 !important;
                }
                
                /* Fix header spacing */
                .filament-header {
                    padding: 0 !important;
                    margin-bottom: 1rem !important;
                }
                
                .filament-header-widget {
                    margin-bottom: 0 !important;
                    padding: 0 !important;
                }
                
                /* Smooth page transitions */
                body {
                    transition: opacity 0.3s ease;
                }
                
                body.page-transitioning {
                    opacity: 0.7;
                    pointer-events: none;
                }
                
                /* Prevent flash during navigation */
                .filament-content {
                    animation: fadeIn 0.3s ease-in-out;
                }
                
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(5px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                /* Fix pipeline page specific issues */
                .filament-page[data-page="SalesPipeline"] .filament-tabs {
                    max-width: 100% !important;
                }
            </style>'
        );
    }
}
