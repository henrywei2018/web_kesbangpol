<?php

namespace App\Traits;

use App\Observers\WhatsAppObserver;
use App\Models\StatusLayanan;

trait HasStatusUpdateNotifications
{
    /**
     * Send WhatsApp notification when status is updated
     */
    public function notifyStatusUpdate(string $newStatus, string $keterangan = ''): void
    {
        $observer = app(WhatsAppObserver::class);
        $observer->handleStatusUpdate($this, $newStatus, $keterangan);
    }

    /**
     * Update status and send WhatsApp notification
     * Handles both morph status models and simple status field models
     */
    public function updateStatusWithNotification(string $newStatus, string $keterangan = ''): bool
    {
        try {
            // Check if model uses morph status relationship
            if ($this->usesMorphStatus()) {
                return $this->updateMorphStatusWithNotification($newStatus, $keterangan);
            } else {
                return $this->updateSimpleStatusWithNotification($newStatus, $keterangan);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update status with notification', [
                'model' => get_class($this),
                'id' => $this->id,
                'status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if model uses morph status relationship
     */
    protected function usesMorphStatus(): bool
    {
        return method_exists($this, 'statuses') && 
               in_array(get_class($this), [
                   'App\Models\PermohonanInformasiPublik',
                   'App\Models\KeberatanInformasiPublik'
               ]);
    }

    /**
     * Update status for models with morph relationship
     */
    protected function updateMorphStatusWithNotification(string $newStatus, string $keterangan): bool
    {
        // Create new status record using morph relationship
        $this->statuses()->create([
            'status' => $newStatus,
            'deskripsi_status' => $keterangan,
        ]);

        // Send WhatsApp notification
        $this->notifyStatusUpdate($newStatus, $keterangan);

        // Refresh model to get latest status
        $this->refresh();

        return true;
    }

    /**
     * Update status for models with simple status field
     */
    protected function updateSimpleStatusWithNotification(string $newStatus, string $keterangan): bool
    {
        // Update the status field directly
        $updateData = ['status' => $newStatus];
        
        // If model has a keterangan/description field, update it too
        if ($this->hasKeteranganField()) {
            $keteranganField = $this->getKeteranganFieldName();
            $updateData[$keteranganField] = $keterangan;
        }

        $this->update($updateData);

        // Send WhatsApp notification
        $this->notifyStatusUpdate($newStatus, $keterangan);

        return true;
    }

    /**
     * Check if model has a keterangan/description field
     */
    protected function hasKeteranganField(): bool
    {
        $keteranganFields = ['keterangan', 'keterangan_status', 'deskripsi_status', 'notes'];
        return collect($keteranganFields)->some(fn($field) => $this->isFillable($field));
    }

    /**
     * Get the name of the keterangan field
     */
    protected function getKeteranganFieldName(): string
    {
        $keteranganFields = ['keterangan', 'keterangan_status', 'deskripsi_status', 'notes'];
        
        foreach ($keteranganFields as $field) {
            if ($this->isFillable($field)) {
                return $field;
            }
        }
        
        return 'keterangan'; // default
    }

    /**
     * Get the latest status record (for morph models)
     */
    public function getLatestStatus(): ?StatusLayanan
    {
        if (!$this->usesMorphStatus()) {
            return null;
        }
        
        return $this->statuses()->latest('created_at')->first();
    }

    /**
     * Get the current status value
     */
    public function getCurrentStatus(): ?string
    {
        if ($this->usesMorphStatus()) {
            $latestStatus = $this->getLatestStatus();
            return $latestStatus ? $latestStatus->status : 'Pending';
        } else {
            // For simple status field models
            return $this->getAttribute('status') ?? 'pending';
        }
    }

    /**
     * Get the current status description
     */
    public function getCurrentStatusDescription(): ?string
    {
        if ($this->usesMorphStatus()) {
            $latestStatus = $this->getLatestStatus();
            return $latestStatus ? $latestStatus->deskripsi_status : '';
        } else {
            // Try to get from keterangan field
            $keteranganField = $this->getKeteranganFieldName();
            return $this->getAttribute($keteranganField) ?? '';
        }
    }

    /**
     * Get all status history (for morph models)
     */
    public function getStatusHistory()
    {
        if (!$this->usesMorphStatus()) {
            return collect([]);
        }
        
        return $this->statuses()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Check if model has a specific status
     */
    public function hasStatus(string $status): bool
    {
        return $this->getCurrentStatus() === $status;
    }

    /**
     * Check if model status is in given array
     */
    public function hasStatusIn(array $statuses): bool
    {
        return in_array($this->getCurrentStatus(), $statuses);
    }

    /**
     * Get status count for dashboard/stats
     */
    public static function getStatusCounts(): array
    {
        $model = new static;
        
        if ($model->usesMorphStatus()) {
            // For morph status models
            $records = static::with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])->get();

            $statusCounts = [];
            
            foreach ($records as $record) {
                $status = $record->getCurrentStatus();
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }

            return $statusCounts;
        } else {
            // For simple status field models
            return static::groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status')
                ->toArray();
        }
    }

    /**
     * Scope to filter by current status (for morph models)
     */
    public function scopeWithCurrentStatus($query, string $status)
    {
        if ($this->usesMorphStatus()) {
            return $query->whereHas('statuses', function ($statusQuery) use ($status) {
                $statusQuery->where('status', $status)
                           ->whereRaw('created_at = (SELECT MAX(created_at) FROM mt_status WHERE layanan_id = ' . $this->getTable() . '.id AND layanan_type = "' . static::class . '")');
            });
        } else {
            return $query->where('status', $status);
        }
    }

    /**
     * Get common status options for forms
     */
    public static function getStatusOptions(): array
    {
        $model = new static;
        
        // Default status options - can be overridden in models
        $defaultOptions = [
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
        ];

        // Check if model has custom status options method
        if (method_exists($model, 'getCustomStatusOptions')) {
            return $model->getCustomStatusOptions();
        }

        return $defaultOptions;
    }

    /**
     * Boot method to ensure the relationship exists for morph models
     */
    protected static function bootHasStatusUpdateNotifications()
    {
        $model = new static;
        
        // Only check for morph models
        if ($model->usesMorphStatus() && !method_exists(static::class, 'statuses')) {
            throw new \Exception('Model ' . static::class . ' must have a statuses() morphMany relationship');
        }
    }
}