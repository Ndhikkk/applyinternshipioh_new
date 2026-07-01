<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;
use App\Models\AppSettingsModel;
use CodeIgniter\Controller;

class Pendaftaran extends Controller
{
    public function index()
    {
        $settingsModel = new AppSettingsModel();
        $data['registration_open'] = $settingsModel->getValue('registration_open') ?? '1';
        return view('pendaftaran', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama_lengkap' => 'required',
            'email' => 'required|valid_email',
            'nomor_whatsapp' => 'required',
            'asal_kampus' => 'required',
            'program_studi' => 'required',
            'semester' => 'required|integer',
            'jenis_magang' => 'required|in_list[Wajib,Mandiri]',
            'periode_mulai' => 'required',
            'periode_selesai' => 'required',
            'cv' => 'uploaded[cv]|max_size[cv,2048]|ext_in[cv,pdf]|mime_in[cv,application/pdf]',
            'surat_pengantar' => 'permit_empty|max_size[surat_pengantar,2048]|ext_in[surat_pengantar,pdf]|mime_in[surat_pengantar,application/pdf]',
            'ktm' => 'permit_empty|max_size[ktm,4096]|ext_in[ktm,pdf,jpg,jpeg,png]|mime_in[ktm,application/pdf,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Upload file ke Local Server (WRITEPATH/uploads)
        $cv = $this->request->getFile('cv');
        $cvName = '';
        if ($cv && $cv->isValid() && !$cv->hasMoved()) {
            $cvName = $cv->getRandomName();
            $cv->move(WRITEPATH . 'uploads/cv', $cvName);
        }

        $surat_pengantar = $this->request->getFile('surat_pengantar');
        $suratName = '';
        if ($surat_pengantar && $surat_pengantar->isValid() && !$surat_pengantar->hasMoved()) {
            $suratName = $surat_pengantar->getRandomName();
            $surat_pengantar->move(WRITEPATH . 'uploads/surat', $suratName);
        }

        $ktm = $this->request->getFile('ktm');
        $ktmName = '';
        if ($ktm && $ktm->isValid() && !$ktm->hasMoved()) {
            $ktmName = $ktm->getRandomName();
            $ktm->move(WRITEPATH . 'uploads/ktm', $ktmName);
        }

        $model = new PendaftaranModel();

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
            'asal_kampus' => $this->request->getPost('asal_kampus'),
            'program_studi' => $this->request->getPost('program_studi'),
            'semester' => $this->request->getPost('semester'),
            'jenis_magang' => $this->request->getPost('jenis_magang'),
            'periode_mulai' => $this->request->getPost('periode_mulai'),
            'periode_selesai' => $this->request->getPost('periode_selesai'),
            'cv' => $cvName, 
            'surat_pengantar' => $suratName,
            'ktm' => $ktmName,
            'status' => 'Menunggu',
            'catatan_admin' => '',
        ];

        $insertId = $model->insert($data);
        $newData = $model->find($insertId);

        return view('pendaftaran_success', ['data' => $newData]);
    }
}