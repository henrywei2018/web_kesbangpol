<?php

namespace App\Providers;

use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

// Import Models for Observer Registration
use App\Models\SKT;
use App\Models\SKL;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use App\Models\LaporATHG; // ✅ ADD this import

// Import Observers
use App\Observers\WhatsAppObserver;
use App\Observers\SKTObserver; // Keep existing SKT observer for ORMAS logic
use App\Observers\SKLObserver; // SKL observer for ORMAS logic

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
        // Configure Filament Tables
        Table::configureUsing(function (Table $table): void {
            $table
                ->emptyStateHeading('No data yet')
                ->striped()
                ->defaultPaginationPageOption(10)
                ->paginated([10, 25, 50, 100])
                ->extremePaginationLinks()
                ->defaultSort('created_at', 'desc');
        });

        // Set Carbon locale
        Carbon::setLocale('id');
        
        // Configure Turnstile
        $siteKey = strval(Config::get('services.turnstile.sitekey'));
        View::share('siteKey', config('services.turnstile.sitekey'));

        View::composer(['*'], function ($view) {
            $view->with('siteKey', config('services.turnstile.sitekey'));
        });

        // Register Model Observers
        $this->registerObservers();
    }

    /**
     * Register all model observers
     */
    private function registerObservers(): void
    {
        // ✅ Register business logic observers first (these are traditional observer classes)
        SKT::observe(SKTObserver::class);  // SKT → ORMAS logic
        SKL::observe(SKLObserver::class);  // SKL → ORMAS logic
        
        // ✅ Register WhatsApp notifications using created event
        $this->registerWhatsAppNotifications();
    }

    /**
     * Register WhatsApp notification events
     */
    private function registerWhatsAppNotifications(): void
    {
        // Get WhatsApp observer instance
        $whatsappObserver = app(WhatsAppObserver::class);
        
        // Register created events for WhatsApp notifications
        SKT::created(function ($skt) use ($whatsappObserver) {
            $whatsappObserver->sktCreated($skt);
        });

        SKL::created(function ($skl) use ($whatsappObserver) {
            $whatsappObserver->sklCreated($skl);
        });

        PermohonanInformasiPublik::created(function ($permohonan) use ($whatsappObserver) {
            $whatsappObserver->permohonanInformasiPublikCreated($permohonan);
        });

        KeberatanInformasiPublik::created(function ($keberatan) use ($whatsappObserver) {
            $whatsappObserver->keberatanInformasiPublikCreated($keberatan);
        });

        LaporATHG::created(function ($laporATHG) use ($whatsappObserver) {
            $whatsappObserver->laporATHGCreated($laporATHG);
        });
    }
}