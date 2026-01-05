<?php

namespace App\Http\Controllers;

use App\Settings\CMSSettings;
use Illuminate\Http\Request;

class CMSController extends Controller
{
    public function profil(CMSSettings $settings)
    {
        return view('cms.profil', [
            'title' => $settings->profil_title,
            'content' => $settings->profil_content,
        ]);
    }

    public function visiMisi(CMSSettings $settings)
    {
        return view('cms.visi-misi', [
            'title' => $settings->visi_misi_title,
            'visi' => $settings->visi_content,
            'misi' => $settings->misi_content,
        ]);
    }

    public function tugasFungsi(CMSSettings $settings)
    {
        return view('cms.tugas-fungsi', [
            'title' => $settings->tugas_fungsi_title,
            'tugas' => $settings->tugas_content,
            'fungsi' => $settings->fungsi_content,
        ]);
    }

    public function strukturOrganisasi(CMSSettings $settings)
    {
        return view('cms.struktur-organisasi', [
            'title' => $settings->struktur_organisasi_title,
            'content' => $settings->struktur_content,
            'image' => $settings->struktur_image,
        ]);
    }
}