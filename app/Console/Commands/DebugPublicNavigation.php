<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Filament\Facades\Filament;

class DebugPublicNavigation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:public-navigation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug public panel navigation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $panel = Filament::getPanel('public');
        
        $this->info('Public Panel Configuration:');
        $this->line('Panel ID: ' . $panel->getId());
        $this->line('Panel Path: ' . $panel->getPath());
        
        $this->info('Registered Resources:');
        foreach ($panel->getResources() as $resource) {
            $this->line('- ' . $resource);
            
            if (method_exists($resource, 'shouldRegisterNavigation')) {
                $shouldRegister = $resource::shouldRegisterNavigation();
                $this->line('  shouldRegisterNavigation: ' . ($shouldRegister ? 'true' : 'false'));
            } else {
                $this->line('  shouldRegisterNavigation: not defined');
            }
            
            if (method_exists($resource, 'getNavigationGroup')) {
                $group = $resource::getNavigationGroup();
                $this->line('  navigationGroup: ' . ($group ?? 'null'));
            }
            
            if (method_exists($resource, 'getNavigationLabel')) {
                $label = $resource::getNavigationLabel();
                $this->line('  navigationLabel: ' . $label);
            }
        }
        
        $this->info('Navigation Groups:');
        foreach ($panel->getNavigationGroups() as $group) {
            $this->line('- ' . $group->getLabel());
        }
        
        return 0;
    }
}
