<?php

namespace App\Http\Controllers;

use App\Models\LaporGiat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileDownloadController extends Controller
{
    /**
     * Download laporan kegiatan PDF
     */
    public function downloadLaporan(LaporGiat $laporGiat)
    {
        // Check permissions
        if (!$this->canAccessFile($laporGiat)) {
            abort(403, 'Unauthorized access to file');
        }

        if (!$laporGiat->laporan_kegiatan_path || !Storage::exists($laporGiat->laporan_kegiatan_path)) {
            abort(404, 'File not found');
        }

        $filename = "Laporan_Kegiatan_{$laporGiat->nama_ormas}_{$laporGiat->id}.pdf";

        return Storage::download($laporGiat->laporan_kegiatan_path, $filename);
    }

    /**
     * Download specific image
     */
    public function downloadImage(LaporGiat $laporGiat, $imageIndex)
    {
        // Check permissions
        if (!$this->canAccessFile($laporGiat)) {
            abort(403, 'Unauthorized access to file');
        }

        if (!$laporGiat->images_paths || !isset($laporGiat->images_paths[$imageIndex])) {
            abort(404, 'Image not found');
        }

        $imagePath = $laporGiat->images_paths[$imageIndex];
        
        if (!Storage::exists($imagePath)) {
            abort(404, 'Image file not found');
        }

        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $filename = "Foto_Kegiatan_{$laporGiat->id}_{$imageIndex}.{$extension}";

        return Storage::download($imagePath, $filename);
    }

    /**
     * Stream image for viewing (inline display)
     */
    public function viewImage(LaporGiat $laporGiat, $imageIndex): StreamedResponse
    {
        // Check permissions
        if (!$this->canAccessFile($laporGiat)) {
            abort(403, 'Unauthorized access to file');
        }

        if (!$laporGiat->images_paths || !isset($laporGiat->images_paths[$imageIndex])) {
            abort(404, 'Image not found');
        }

        $imagePath = $laporGiat->images_paths[$imageIndex];
        
        if (!Storage::exists($imagePath)) {
            abort(404, 'Image file not found');
        }

        $mimeType = Storage::mimeType($imagePath);

        return Storage::response($imagePath, null, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * View PDF inline
     */
    public function viewLaporan(LaporGiat $laporGiat): StreamedResponse
    {
        // Check permissions
        if (!$this->canAccessFile($laporGiat)) {
            abort(403, 'Unauthorized access to file');
        }

        if (!$laporGiat->laporan_kegiatan_path || !Storage::exists($laporGiat->laporan_kegiatan_path)) {
            abort(404, 'File not found');
        }

        return Storage::response($laporGiat->laporan_kegiatan_path, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Download all images as ZIP
     */
    public function downloadAllImages(LaporGiat $laporGiat)
    {
        // Check permissions
        if (!$this->canAccessFile($laporGiat)) {
            abort(403, 'Unauthorized access to files');
        }

        if (!$laporGiat->images_paths || empty($laporGiat->images_paths)) {
            abort(404, 'No images found');
        }

        $zip = new \ZipArchive();
        $zipFileName = "Foto_Kegiatan_{$laporGiat->id}_" . now()->format('Y-m-d') . ".zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");

        // Create temp directory if it doesn't exist
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($laporGiat->images_paths as $index => $imagePath) {
                if (Storage::exists($imagePath)) {
                    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $fileName = "foto_kegiatan_{$index}.{$extension}";
                    $zip->addFile(Storage::path($imagePath), $fileName);
                }
            }
            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        abort(500, 'Could not create ZIP file');
    }

    /**
     * Check if user can access file
     */
    private function canAccessFile(LaporGiat $laporGiat): bool
    {
        $user = Auth::user();

        // Super admin can access all files
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin can access all files
        if ($user->hasRole('admin')) {
            return true;
        }

        // User can only access their own files
        return $laporGiat->user_id === $user->id;
    }
}