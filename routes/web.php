<?php

use App\Filament\Pages\Auth\Login;
use App\Models\Spt;
use App\Models\SKLDocumentFeedback;
use App\Livewire\SignaturePad;
use App\Livewire\SptPdfComponent;
use App\Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Livewire\PostIndex;
use App\Livewire\PostShow;
use App\Http\Controllers\Auth\EmailVerificationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// //     })->name('welcome'); 
// Route::get('admin/login', Login::class)->name('login');    
// Route::get('/admin/register', Register::class)->name('register');
Route::get('/admin/email-verification/verify/{hash}', EmailVerificationController::class)
    ->name('filament.admin.auth.email-verification.verify.custom');
Route::get('/', App\Livewire\Home::class)->name('beranda');
Route::get('/profil', App\Livewire\ProfilOrganisasi::class)->name('profil.organisasi');
Route::get('/visi-misi', App\Livewire\VisiMisi::class)->name('visi.misi');
Route::get('/tugas-fungsi', App\Livewire\TugasFungsi::class)->name('tugas.fungsi');
Route::get('/struktur-organisasi',App\Livewire\StrukturOrganisasi::class)->name('struktur.organisasi');
Route::get('/blog', App\Livewire\PostIndex::class)->name('post.index');
Route::get('/blog/{slug}', App\Livewire\PostShow::class)->name('post.show');
Route::get('/galeri', App\Livewire\GaleriIndex::class)->name('galeri.index');
Route::get('/galeri/{slug}', App\Livewire\GaleriShow::class)->name('galeri.show');
Route::get('/infografis', App\Livewire\InfographicIndex::class)->name('infographic.index');
Route::get('/infografis/{slug}', App\Livewire\InfographicShow::class)->name('infographic.show');
Route::get('/publikasi', App\Livewire\PublikasiIndex::class)->name('publikasi.index');
Route::get('/layanan-ppid', App\Livewire\PagePPID::class)->name('layanan.ppid');
Route::get('/layanan-skt', App\Livewire\LayananSkt::class)->name('layanan.skt');
Route::get('/layanan-skl', App\Livewire\LayananSkl::class)->name('layanan.skl');
Route::get('/layanan-aduan', App\Livewire\LayananAduan::class)->name('layanan.aduan');
Route::get('/skl/cetak/{id}', [App\Filament\Resources\SKLDocumentFeedbackResource::class, 'print'])->name('skl.print');
Route::get('/skt/cetak/{id}', [App\Filament\Resources\SKTDocumentFeedbackResource::class, 'print'])->name('skt.print');
Route::get('/admin/spts/filtered-pdf', [App\Filament\Resources\SptResource::class, 'generateFilteredPdf'])->name('spt.filtered-pdf');

Route::get('/kontak-kami', App\Livewire\KontakComponent::class)->name('kontak.kami');
Route::post('/kontak-kami/submit', [App\Livewire\ContactForm::class,'submitForm'])->middleware('throttle:contact-form');

Route::get('/spt/signature/pa/{id}', [App\Http\Controllers\SignatureController::class, 'showSignaturePage'])
    ->name('spt.signature.pa')
    ->middleware('signed');

Route::post('/spt/signature/pa/{id}', [App\Http\Controllers\SignatureController::class, 'saveSignature'])
    ->name('spt.signature.pa.save');
    
Route::get('/spt/signature/pptk/{id}', [App\Http\Controllers\SignatureController::class, 'showSignaturePage'])
    ->name('spt.signature.pptk')
    ->middleware('signed');

Route::post('/spt/signature/pptk/{id}', [App\Http\Controllers\SignatureController::class, 'saveSignature'])
    ->name('spt.signature.pptk.save');
    
Route::get('/admin/spts/{sptId}/cetakspt', [App\Filament\Resources\SPTResource::class, 'showPdf'])->name('spt.show-pdf')
    ->middleware('auth');
Route::get('/admin/spts/{sptId}/cetaksppd', [App\Filament\Resources\SPTResource::class, 'showSppdPdf'])->name('sppd.show-pdf')
    ->middleware('auth');