<?php

namespace App\Livewire;

use Livewire\Component;

class PublicAduan extends Component
{
    public $judul;
    public $kategori;
    public $deskripsi;
    public $ticket;
    public $status;
    public $search_ticket;
    public $email;
    public $phone_number;

    protected $rules = [
        'judul' => 'required|string|max:255',
        'kategori' => 'required|in:Aduan,Aspirasi,Kritik,Lainnya',
        'deskripsi' => 'required|string|max:2000',
        'email' => 'nullable|email|max:255',
        'phone_number' => 'nullable|regex:/^[0-9]{10,15}$/',
    ];

    public function submitAduan()
    {
        $this->validate();

        // Handle user identification
        $userId = null;
        if (auth()->check()) {
            $userId = auth()->id();
        } elseif ($this->email && $this->phone_number) {
            $user = User::firstOrCreate(
                ['email' => $this->email],
                ['phone_number' => $this->phone_number, 'name' => 'Guest User']
            );
            $userId = $user->id;
        }

        if (!$userId) {
            session()->flash('error', 'Please provide valid email and phone number.');
            return;
        }

        // Generate a unique ticket number
        $this->ticket = strtoupper(uniqid());

        // Save Aduan
        Aduan::create([
            'user_id' => $userId,
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'status' => 'pengajuan',
            'ticket' => $this->ticket,
        ]);

        // Reset form fields and show success message
        $this->reset(['judul', 'kategori', 'deskripsi', 'email', 'phone_number']);
        session()->flash('message', "Aduan Anda telah diterima! Nomor Tiket: {$this->ticket}");
    }

    public function trackAduan()
    {
        $aduan = Aduan::where('ticket', $this->search_ticket)->first();

        if ($aduan) {
            $this->status = $aduan->status;
        } else {
            $this->status = 'Tidak ditemukan';
        }
    }

    public function render()
    {
        return view('livewire.public-aduan');
    }
}