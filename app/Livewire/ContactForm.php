<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Aduan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use DOMPurifier;

class ContactForm extends Component
{
    public $nama;
    public $email;
    public $telpon;
    public $judul;
    public $kategori;
    public $deskripsi;
    public $turnstileResponse;
    public $showForm = true;
    public $submittedAduan = null;
    public $ticketNumber = '';
    public $activeTab = 'submit'; 
    public $searchTicket = ''; 
    public $searchResult = null;

    protected function rules()
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\-\'\.]+$/u', // Only letters, spaces, hyphens, apostrophes, dots
            ],
            'telpon' => [
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'max:20',
                'min:10',
            ],
            'email' => [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
                'not_regex:/\b(?:admin|support|info|contact)\b/i', // Prevent common spam emails
            ],
            'judul' => [
                'required',
                'string',
                'max:255',
                'not_regex:/<[^>]*>/', // Prevent HTML injection
            ],
            'kategori' => [
                'required',
                'string',
                'max:50',
                Rule::in(array_keys(Aduan::KATEGORI_LIST)),
            ],
            'deskripsi' => [
                'required',
                'string',
                'max:5000',
                'not_regex:/<(script|iframe|object|embed|form)/i', // Prevent dangerous HTML
            ],
            'turnstileResponse' => ['required_if:isLocalEnv,false'],
        ];
    }

    public function messages()
    {
        return [
            // ... your existing messages ...
            'nama.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan tanda baca',
            'telpon.min' => 'Nomor telepon minimal 10 digit',
            'email.email' => 'Format email tidak valid',
            'email.dns' => 'Domain email tidak valid',
            'email.not_regex' => 'Email ini tidak diizinkan',
            'judul.not_regex' => 'Judul mengandung karakter yang tidak diizinkan',
            'deskripsi.not_regex' => 'Deskripsi mengandung konten yang tidak diizinkan',
        ];
    }

    protected function sanitizeInput($input)
    {
        if (is_string($input)) {
            // Remove invisible/control characters
            $input = preg_replace('/[\x00-\x1F\x7F]/u', '', $input);
            
            // Convert special characters to HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            // Remove potential SQL injection patterns
            $input = preg_replace('/\b(union|select|insert|update|delete|drop|truncate)\b/i', '', $input);
            
            return trim($input);
        }
        return $input;
    }

    public function updated($propertyName)
    {
        // Real-time validation
        $this->validateOnly($propertyName);
        
        // Sanitize input immediately
        $this->{$propertyName} = $this->sanitizeInput($this->{$propertyName});
    }

    public function mount()
    {
        // Initialize honeypot (hidden field)
        $this->dispatch('initHoneypot');
        
        if (app()->environment('local')) {
            $this->resetForm();
        }
    }

    protected function checkSpam()
    {
        // Check submission speed (too fast = likely bot)
        if (session()->has('form_loaded_at')) {
            $loadedAt = session()->get('form_loaded_at');
            if (time() - $loadedAt < 3) { // Less than 3 seconds
                return false;
            }
        }

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/\b(?:viagra|casino|forex)\b/i',
            '/https?:\/\//i', // URLs in text
            '/\[url=/i',
            '/\[link=/i',
        ];

        foreach ([$this->nama, $this->judul, $this->deskripsi] as $field) {
            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $field)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function isValidRequest(): bool
    {
        // Check if the request is coming from a valid session
        if (!session()->has('_token')) {
            return false;
        }

        // Check if the request has a valid referrer
        $referrer = request()->header('referer');
        if (!$referrer || !str_starts_with($referrer, config('app.url'))) {
            return false;
        }

        // Check request method
        if (!request()->isMethod('post')) {
            return false;
        }

        return true;
    }

    public function submitForm()
    {
        // Verify request integrity
        if (!$this->isValidRequest()) {
            Log::warning('Invalid form request detected', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            session()->flash('error', 'Permintaan tidak valid.');
            return;
        }

        // Check for spam
        if (!$this->checkSpam()) {
            Log::warning('Potential spam detected', [
                'ip' => request()->ip(),
                'content' => [
                    'nama' => $this->nama,
                    'judul' => $this->judul
                ]
            ]);
            session()->flash('error', 'Permintaan tidak dapat diproses.');
            return;
        }

        // Validate inputs
        $this->validate();

        // Rate limiting
        $rateLimitKey = 'aduan-form|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $remaining = RateLimiter::availableIn($rateLimitKey);
            session()->flash('error', "Terlalu banyak permintaan. Silakan coba lagi dalam {$remaining} detik.");
            return;
        }

        // Turnstile validation
        if (!app()->environment('local') && !$this->validateTurnstile()) {
            return;
        }

        try {
            // Sanitize all inputs before saving
            $formattedPhone = '+62' . preg_replace('/[^0-9]/', '', $this->sanitizeInput($this->telpon));
            $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            
            $aduan = Aduan::create([
                'nama' => $this->sanitizeInput($this->nama),
                'email' => $this->sanitizeInput($this->email),
                'telpon' => $formattedPhone,
                'judul' => $this->sanitizeInput($this->judul),
                'kategori' => $this->sanitizeInput($this->kategori),
                'deskripsi' => $this->sanitizeInput($this->deskripsi),
                'status' => 'pengajuan',
                'ticket' => $ticketNumber,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent(), 0, 255)
            ]);

            // Hit rate limiter
            RateLimiter::hit($rateLimitKey, 300); // 5 minutes

            $this->submittedAduan = $aduan;
            $this->ticketNumber = $ticketNumber;
            $this->showForm = false;

            // Clear sensitive data
            $this->reset(['nama', 'email', 'telpon', 'deskripsi']);

        } catch (\Exception $e) {
            Log::error('Form submission error', [
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);
            session()->flash('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    public function autofill()
    {
        if (app()->environment('local')) {
            $this->nama = "John Doe";
            $this->email = "john@example.com";
            $this->telpon = "812-3456-7890";
            $this->judul = "Test Aduan";
            $this->kategori = "Aduan";
            $this->deskripsi = "This is a test description for development purposes.";
        }
    }

    public function render()
    {
        $siteKey = strval(Config::get('services.turnstile.sitekey'));
        return view('livewire.contact-form', [
            'siteKey' => $siteKey,
            'kategoriOptions' => Aduan::KATEGORI_LIST,
            'isLocalEnv' => app()->environment('local')
        ])->layout('components.layouts.app'); // Remove siteKey from layout parameters
    }
}