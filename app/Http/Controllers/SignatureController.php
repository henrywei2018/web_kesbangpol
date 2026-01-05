<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use App\Models\Pegawai;
use App\Models\SptPegawai;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SignatureController extends Controller
{
    public function showSignaturePage(Request $request, $id)
{   
    $role = $request->query('role');
    $spt = Spt::with(['rekening', 'pengesah'])->findOrFail($id);

    // Fetch associated SPT Pegawai records
    $sptPegawaiRecords = SptPegawai::with(['pegawai', 'spt'])
        ->where('spt_id', $id)
        ->get();

    // Pass the role (PA or PPTK) to the view
    return view('signature-pad', [
        'spt' => $spt,
        'sptPegawaiRecords' => $sptPegawaiRecords,
        'role' => $role // Pass role to Blade
    ]);
}

public function saveSignature(Request $request, $id)
{
    $role = $request->input('role');
    $request->validate([
        'signature' => 'required|string', // Signature sent as base64 string
    ]);

    // Find the associated SPT
    $spt = Spt::findOrFail($id);
    if ($role == 'PA') {
    // Fetch signer for PA
    $signer = Pegawai::findOrFail($spt->pengesah_id);
} else {
    // Fetch signer for PPTK via 'rekening->pegawai'
    $signer = $spt->rekening->pegawai;

    if (!$signer) {
        // If pegawai is not available via 'rekening->pegawai', search via kode_rekening
        $signer = Pegawai::where('kode_rekening', $spt->kode_rekening)->first();

        if (!$signer) {
            // Return an error if PPTK Pegawai is not found
            return response()->json(['status' => 'error', 'message' => 'PPTK Pegawai not found.'], 404);
        }
    }
}

// You can now use $signer->id as the pegawai_id
$pegawaiId = $signer->id;


    // Get base64 signature data from the request
    $data_uri = $request->input('signature');
    $encoded_image = explode(",", $data_uri)[1]; // Split metadata and base64 data
    $decoded_image = base64_decode($encoded_image);

    // Set the unique file name based on the role (PA or PPTK)
    $signatureFileName = "signatures/spt_{$spt->id}/{$role}/" . time() . ".png";

    // Store the signature image in local storage
    Storage::disk('local')->put($signatureFileName, $decoded_image);

    // Create or update the signature based on spt_id and signed_as
    $signature = Signature::updateOrCreate(
        [
            'spt_id' => $spt->id,
            'signed_as' => $role, // Use role (PA or PPTK)
        ],
        [
            'pegawai_id' => ($role == 'PA') ? $spt->pengesah_id : $pegawaiId,
            'signed_path' => $signatureFileName,
            
        ]
    );

    // Update the SPT table with the signature id and status
    $spt->signature_data = $signature->id;
    $spt->status_spt = 'setuju'; // Mark status as "approved" after signing
    $spt->save();

    // Notify the user that the signature has been saved
    Notification::make()
        ->title('Notifikasi')
        ->success()
        ->body("{$role} telah menandatangani dokumen dengan nomor SPT {$spt->nomor_spt}.")
        ->send();

    // Return JSON for AJAX
    return response()->json(['status' => 'success', 'message' => 'Tanda tangan berhasil disimpan!']);
}

}