<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\SKT;
use App\Models\SKL;
use App\Models\OrmasMaster;

class UniqueOrmasNameRule implements Rule
{
    protected $ignoreId;
    protected $sourceType;
    
    public function __construct($ignoreId = null, $sourceType = null)
    {
        $this->ignoreId = $ignoreId;
        $this->sourceType = $sourceType;
    }

    public function passes($attribute, $value)
    {
        if (empty(trim($value))) {
            return false; // Empty names are not allowed
        }

        $normalizedValue = $this->normalizeOrmasName($value);
        
        // Check existing ORMAS master records
        $existingOrmas = OrmasMaster::whereRaw('LOWER(TRIM(nama_ormas)) = ?', [strtolower(trim($value))])
            ->when($this->ignoreId && $this->sourceType, function($query) {
                if ($this->sourceType === 'skt') {
                    return $query->where('skt_id', '!=', $this->ignoreId);
                } else {
                    return $query->where('skl_id', '!=', $this->ignoreId);
                }
            })
            ->exists();

        if ($existingOrmas) {
            return false;
        }

        // Check existing SKT records
        $existingSKT = SKT::whereRaw('LOWER(TRIM(nama_ormas)) = ?', [strtolower(trim($value))])
            ->when($this->ignoreId && $this->sourceType === 'skt', function($query) {
                return $query->where('id', '!=', $this->ignoreId);
            })
            ->exists();

        if ($existingSKT) {
            return false;
        }

        // Check existing SKL records
        $existingSKL = SKL::whereRaw('LOWER(TRIM(nama_organisasi)) = ?', [strtolower(trim($value))])
            ->when($this->ignoreId && $this->sourceType === 'skl', function($query) {
                return $query->where('id', '!=', $this->ignoreId);
            })
            ->exists();

        return !$existingSKL;
    }

    public function message()
    {
        return 'Nama organisasi sudah ada dalam sistem. Silakan gunakan nama yang berbeda.';
    }

    private function normalizeOrmasName($name)
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $name)));
    }
}