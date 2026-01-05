<?php

use App\Models\Spt;
use App\Models\SKLDocumentFeedback;
use App\Livewire\SignaturePad;
use App\Livewire\SptPdfComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\PostIndex;
use App\Livewire\PostShow;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\FileDownloadController;
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
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
});

// Logout Route
Route::middleware('auth')->group(function () {
    // Standard logout
    Route::post('/logout', LogoutController::class)->name('logout');
    
    // Filament admin logout (if Filament tries to use this)
    Route::post('/admin/logout', LogoutController::class)->name('filament.admin.auth.logout');
    
    // Alternative logout routes
    Route::get('/logout', LogoutController::class)->name('logout.get');
    Route::get('/admin/logout', LogoutController::class)->name('admin.logout');
});
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
Route::get('/pokuskaltara', App\Livewire\PokusKaltara::class)->name('pokus.kaltara');
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
    
Route::middleware(['auth'])->group(function () {
    // Laporan Kegiatan file routes
    Route::get('/lapor-giat/{laporGiat}/download-laporan', [FileDownloadController::class, 'downloadLaporan'])
        ->name('lapor-giat.download-laporan');
    
    Route::get('/lapor-giat/{laporGiat}/view-laporan', [FileDownloadController::class, 'viewLaporan'])
        ->name('lapor-giat.view-laporan');
    
    Route::get('/lapor-giat/{laporGiat}/download-image/{imageIndex}', [FileDownloadController::class, 'downloadImage'])
        ->name('lapor-giat.download-image');
    
    Route::get('/lapor-giat/{laporGiat}/view-image/{imageIndex}', [FileDownloadController::class, 'viewImage'])
        ->name('lapor-giat.view-image');
    
    Route::get('/lapor-giat/{laporGiat}/download-all-images', [FileDownloadController::class, 'downloadAllImages'])
        ->name('lapor-giat.download-all-images');
});
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect /admin to the custom dashboard
    Route::redirect('/admin', '/admin/admin-dashboard');
    
    // Custom dashboard route
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
    
    // ORMAS Certificate Route
    Route::get('/ormas/{ormas}/certificate', function ($ormas) {
        // TODO: Implement actual certificate generation
        return response()->json([
            'message' => 'Certificate generation for ORMAS ID: ' . $ormas,
            'note' => 'This is a placeholder - implement actual certificate generation'
        ]);
    })->name('ormas.certificate');
});