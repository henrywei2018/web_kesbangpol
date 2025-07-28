<?php

namespace App\Filament\Public\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemStatus extends Widget
{
    protected static string $view = 'filament.public.widgets.system-status';
    
    protected int | string | array $columnSpan = 1;

    public function getViewData(): array
    {
        return [
            'database_status' => $this->checkDatabaseStatus(),
            'cache_status' => $this->checkCacheStatus(),
            'response_time' => $this->getResponseTime(),
        ];
    }
    
    private function checkDatabaseStatus(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'online', 'message' => 'Normal'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Error'];
        }
    }
    
    private function checkCacheStatus(): array
    {
        try {
            Cache::put('system_check', 'ok', 60);
            $result = Cache::get('system_check');
            return $result === 'ok' 
                ? ['status' => 'online', 'message' => 'Working'] 
                : ['status' => 'offline', 'message' => 'Error'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Error'];
        }
    }
    
    private function getResponseTime(): string
    {
        $start = microtime(true);
        // Simulate a small operation
        usleep(1000); // 1ms
        $end = microtime(true);
        
        $time = ($end - $start) * 1000; // Convert to milliseconds
        
        if ($time < 100) {
            return 'Fast';
        } elseif ($time < 500) {
            return 'Normal';
        } else {
            return 'Slow';
        }
    }
}