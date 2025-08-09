<?php

namespace App\Providers;

use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Observers\WhatsAppObserver;
use App\Models\SKT;
use App\Models\SKL;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use App\Observers\SKTObserver;
use App\Observers\SKLObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerObservers();
        Table::configureUsing(function (Table $table): void {
            $table
                ->emptyStateHeading('No data yet')
                ->striped()
                ->defaultPaginationPageOption(10)
                ->paginated([10, 25, 50, 100])
                ->extremePaginationLinks()
                ->defaultSort('created_at', 'desc');
        });
        Carbon::setLocale('id');
        
        $siteKey = strval(Config::get('services.turnstile.sitekey'));
        View::share('siteKey', config('services.turnstile.sitekey'));

        // Or use view composer if you prefer
        View::composer(['*'], function ($view) {
            $view->with('siteKey', config('services.turnstile.sitekey'));
        });
    }
    private function registerObservers(): void
    {
        // WhatsApp notifications observer
        $whatsappObserver = app(WhatsAppObserver::class);
        
        // Register WhatsApp observer methods for each model
        SKT::observe([
            'created' => [$whatsappObserver, 'sktCreated'],
        ]);

        SKL::observe([
            'created' => [$whatsappObserver, 'sklCreated'],
        ]);

        PermohonanInformasiPublik::observe([
            'created' => [$whatsappObserver, 'permohonanInformasiPublikCreated'],
        ]);

        KeberatanInformasiPublik::observe([
            'created' => [$whatsappObserver, 'keberatanInformasiPublikCreated'],
        ]);

        // ORMAS business logic observers for both SKT and SKL
        SKT::observe(SKTObserver::class);  // SKT → ORMAS logic
        SKL::observe(SKLObserver::class);  // SKL → ORMAS logic
    }
}
