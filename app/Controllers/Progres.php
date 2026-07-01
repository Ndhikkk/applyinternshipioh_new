<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;

class Progres extends BaseController
{
    protected $pendaftaranModel;

    public function __construct()
    {
        $this->pendaftaranModel = new PendaftaranModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Cek Progres Pendaftaran - Indosat Ooredoo Hutchison'
        ];

        return view('progres', $data);
    }

    public function cek()
    {
        $nama = $this->request->getPost('nama');

        if (empty(trim($nama))) {
            return redirect()->back()->with('error', 'Nama harus diisi');
        }

        // Cari data pendaftaran berdasarkan nama
        $pendaftaran = $this->pendaftaranModel->like('nama_lengkap', $nama)->first();

        if ($pendaftaran) {
            // DEBUG: Simpan data debug ke session
            session()->setFlashdata('debug_data', [
                'nama' => $pendaftaran['nama_lengkap'] ?? '',
                'Email_raw' => $pendaftaran['Email'] ?? 'NULL',
                'Email_isset' => isset($pendaftaran['Email']) ? 'YES' : 'NO',
                'Email_empty' => empty($pendaftaran['Email']) ? 'YES' : 'NO',
                'Email_trimmed' => trim($pendaftaran['Email'] ?? ''),
                'all_data' => $pendaftaran
            ]);

            $data = [
                'title' => 'Hasil Pencarian - Indosat Ooredoo Hutchison',
                'pendaftaran' => $pendaftaran
            ];
            return view('progres', $data);
        } else {
            $data = [
                'title' => 'Cek Progres Pendaftaran - Indosat Ooredoo Hutchison',
                'notFound' => true,
                'searchTerm' => $nama
            ];
            return view('progres', $data);
        }
    }

    // Tambahkan method ini di Progres Controller jika diperlukan
    public function cekStatus()
    {
        // Redirect ke method cek yang sudah ada
        return $this->cek();
    }
}