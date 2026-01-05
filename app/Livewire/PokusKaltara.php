<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrmasMaster;
use App\Models\SKT;
use App\Models\SKL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PokusKaltara extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $selectedRegion = '';
    public $selectedCategory = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $stats = [];
    public $regions = [];
    public $categories = [];
    public $activeTab = 'overview'; // Default active tab
    
    protected $queryString = [
        'searchTerm' => ['except' => '', 'as' => 'search'],
        'selectedRegion' => ['except' => '', 'as' => 'region'],
        'selectedCategory' => ['except' => '', 'as' => 'category'],
        'activeTab' => ['except' => 'overview', 'as' => 'tab'],
    ];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->loadStats();
        $this->loadFilterOptions();
    }

    public function loadStats()
    {
        $this->stats = Cache::remember('pokus_stats', 300, function () {
            return [
                'total_ormas' => OrmasMaster::count(),
                'ormas_aktif' => OrmasMaster::where('status_administrasi', 'selesai')->count(),
                'ormas_proses' => OrmasMaster::where('status_administrasi', 'belum_selesai')->count(),
                'total_skt' => SKT::count(),
                'total_skl' => SKL::count(),
                'total_dokumen' => SKT::count() + SKL::count(),
            ];
        });
    }

    public function loadFilterOptions()
    {
        $this->regions = Cache::remember('pokus_regions', 600, function () {
            return OrmasMaster::select('kab_kota', DB::raw('count(*) as total'))
                ->whereNotNull('kab_kota')
                ->where('kab_kota', '!=', '')
                ->groupBy('kab_kota')
                ->orderBy('total', 'desc')
                ->get()
                ->toArray();
        });

        $this->categories = Cache::remember('pokus_categories', 600, function () {
            return OrmasMaster::select('ciri_khusus', DB::raw('count(*) as total'))
                ->whereNotNull('ciri_khusus')
                ->where('ciri_khusus', '!=', '')
                ->groupBy('ciri_khusus')
                ->orderBy('total', 'desc')
                ->get()
                ->toArray();
        });
    }

    public function getOrmasProperty()
    {
        $query = OrmasMaster::query()
            ->select([
                'id', 'nomor_registrasi', 'nama_ormas', 'nama_singkatan_ormas',
                'status_administrasi', 'kab_kota', 'ciri_khusus', 'tanggal_pendirian',
                'sumber_registrasi', 'last_updated_from_source_at', 'created_at'
            ]);

        if ($this->searchTerm) {
            $searchTerm = '%' . $this->searchTerm . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_ormas', 'like', $searchTerm)
                  ->orWhere('nama_singkatan_ormas', 'like', $searchTerm)
                  ->orWhere('nomor_registrasi', 'like', $searchTerm);
            });
        }

        if ($this->selectedRegion) {
            $query->where('kab_kota', $this->selectedRegion);
        }

        if ($this->selectedCategory) {
            $query->where('ciri_khusus', $this->selectedCategory);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        if ($this->sortBy !== 'created_at') {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate(12);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectedRegion()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->selectedRegion = '';
        $this->selectedCategory = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pokus-kaltara', [
            'ormas' => $this->ormas,
            'stats' => $this->stats,
            'regions' => $this->regions,
            'categories' => $this->categories,
        ])->layout('components.layouts.app');
    }
}