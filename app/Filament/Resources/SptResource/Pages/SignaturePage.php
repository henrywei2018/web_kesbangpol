<?php

namespace App\Filament\Resources\SptResource\Pages;

use App\Filament\Resources\SptResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\URL;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Models\Spt;
use App\Models\SptPegawai;
use App\Models\Signature;
use Illuminate\Support\Facades\Log;




class SignaturePage extends Page implements HasForms
{
    use InteractsWithForms;

    public $spt;
    public $sptPegawaiRecords;
    public $signature;

    public function mount($id)
    {
        if (! request()->hasValidSignature()) {
            abort(403, 'Link expired or invalid');
        }
    
        // Mengambil data SPT dan Pegawai
        $this->spt = Spt::findOrFail($id);
        $this->sptPegawaiRecords = SptPegawai::with(['pegawai', 'spt'])
                             ->where('spt_id', $id)
                             ->get();
        Log::info('SPT:', [$this->spt]);
        Log::info('SPT Pegawai Records:', $this->sptPegawaiRecords->toArray());
    }

    // Definisikan schema form untuk signature pad
    protected function getFormSchema(): array
    {
        return [
            SignaturePad::make('signature')
                ->label('Sign here')
                ->required()
                ->penColor('#2C4C9C') // Warna pena untuk signature
        ];
    }

    public function submit()
    {
        Signature::create([
            'id_spt' => $this->spt->id,
            'id_sppd' => '0',
            'id_pegawai' => $this->spt->pengesah_id,
            'signed_as' => 'PA',
            'signature_path' => $this->form->getState()['signature'], // Mengambil signature dari form
        ]);

        Notification::make()
            ->title('Berhasil Mengesahkan')
            ->success()
            ->body('Tanda tangan telah tersimpan.')
            ->send();
    }

    // Render halaman dan passing data ke view
    public function render(): View
    {
        return view('filament.resources.spt-resource.pages.signature-page', [
            'spt' => $this->spt,
            'sptPegawaiRecords' => $this->sptPegawaiRecords
            
        ]);
    }
}