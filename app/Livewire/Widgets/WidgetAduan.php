<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\Aduan;
use Filament\Notifications\Notification;

class WidgetAduan extends Component
{
    public $totalAduan;
    public $aduanSelesai;
    public $aduanProses;
    public $completionPercentage;

    public function mount()
    {
        // Count total aduan
        $this->totalAduan = Aduan::count();

        // Count aduan with status 'selesai'
        $this->aduanSelesai = Aduan::where('status', 'selesai')->count();

        $this->completionPercentage = $this->totalAduan > 0
            ? round(($this->aduanSelesai / $this->totalAduan) * 100, 2)
            : 0;
        // Get all aduan where status is not 'selesai'
        $this->aduanProses = Aduan::where('status', '!=', 'selesai')->get();
        
    }
    public function updateStatusToSelesai($id)
    {
        $aduan = Aduan::find($id);

        if ($aduan) {
            $aduan->status = 'selesai';
            $aduan->save();

            Notification::make()
                ->title('Status Updated')
                ->body("The status of Aduan #{$id} has been updated to 'Selesai'.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Update Failed')
                ->body('The specified Aduan could not be found.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.widgets.widget-aduan', [
            'aduanProses' => $this->aduanProses,
        ]);
    }
}
