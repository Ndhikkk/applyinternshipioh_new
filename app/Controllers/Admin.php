<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\PendaftaranModel;
use App\Models\AppSettingsModel;

class Admin extends BaseController
{
    protected $adminModel;
    protected $pendaftaranModel;
    protected $settingsModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->pendaftaranModel = new PendaftaranModel();
        $this->settingsModel = new AppSettingsModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        return redirect()->to('/admin/login');
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        // Jika belum login, tampilkan halaman login
        return view('admin/login');
    }
    public function loginProcess()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validasi input
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password harus diisi.');
        }

        $admin = $this->adminModel->where('username', $username)->first();

        if ($admin && password_verify($password, $admin['password'])) {

            // Set session admin
            $sessionData = [
                'admin_id' => $admin['id'],
                'admin_username' => $admin['username'],
                'admin_nama' => $admin['username'],
                'admin_logged_in' => true
            ];
            session()->set($sessionData);

            return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil!');
        }

        return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
    }

    public function dashboard()
    {
        // Cek apakah admin sudah login
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // DATA STATISTIK YANG DIPERBAIKI
        $data['total_pendaftar'] = $this->pendaftaranModel->countAll();

        // DITERIMA: termasuk Diterima dan Lolos_Final
        $data['total_diterima'] = $this->pendaftaranModel
            ->where('status', 'Diterima')
            ->orWhere('status', 'Lolos_Final')
            ->countAllResults();

        // DITOLAK: termasuk semua status penolakan
        $data['total_ditolak'] = $this->pendaftaranModel
            ->where('status', 'Ditolak')
            ->orWhere('status', 'Tidak_Lolos_Interview_1')
            ->orWhere('status', 'Tidak_Lolos_Interview_2')
            ->orWhere('status', 'Tidak_Lolos_Interview_3')
            ->countAllResults();

        // MENUNGGU: status selain diterima dan ditolak
        $data['total_menunggu'] = $this->pendaftaranModel
            ->whereNotIn('status', [
                'Diterima',
                'Lolos_Final',
                'Ditolak',
                'Tidak_Lolos_Interview_1',
                'Tidak_Lolos_Interview_2',
                'Tidak_Lolos_Interview_3'
            ])
            ->countAllResults();

        // Data pendaftar
        $data['pendaftaran'] = $this->pendaftaranModel->orderBy('created_at', 'DESC')->findAll();

        // Get Registration Status
        $data['registration_open'] = $this->settingsModel->getValue('registration_open') ?? '1';


        return view('admin/dashboard', $data);
    }
    public function updateStatus($id, $status)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pendaftaranModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Status berhasil diupdate!');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupdate status.');
        }
    }

    public function detail($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $data['item'] = $this->pendaftaranModel->find($id);

        if (!$data['item']) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        return view('admin/detail', $data);
    }

    public function exportExcel()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pendaftaran = $this->pendaftaranModel->orderBy('created_at', 'DESC')->findAll();

        // Header untuk file CSV
        $filename = 'data_pendaftaran_magang_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Header CSV - TANPA KODE PENDAFTARAN
        fputcsv($output, [
            'No',
            'Nama',
            'Email',
            'WhatsApp',
            'Kampus',
            'Program Studi',
            'Semester',
            'Jenis Magang',
            'Status',
            'Periode Magang',
            'Tanggal Daftar'
        ]);

        // Data
        $no = 1;
        foreach ($pendaftaran as $row) {
            fputcsv($output, [
                $no++,
                $row['nama_lengkap'],
                $row['Email'] ?? '-',
                $row['nomor_whatsapp'],
                $row['asal_kampus'],
                $row['program_studi'],
                $row['semester'],
                $row['jenis_magang'] ?? '-',
                $row['status'],
                $row['periode_mulai'] . ' to ' . $row['periode_selesai'],
                date('d/m/Y H:i', strtotime($row['created_at']))
            ]);
        }

        fclose($output);
        exit;
    }

    public function delete($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pendaftaran = $this->pendaftaranModel->find($id);

        if ($pendaftaran) {
            // Hapus file yang diupload
            $files = ['cv', 'surat_pengantar', 'ktm'];
            foreach ($files as $file) {
                if (isset($pendaftaran[$file]) && file_exists(WRITEPATH . $pendaftaran[$file])) {
                    unlink(WRITEPATH . $pendaftaran[$file]);
                }
            }

            $this->pendaftaranModel->delete($id);
            return redirect()->to('/admin/dashboard')->with('success', 'Data berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('success', 'Logout berhasil!');
    }
    // Tambahkan method ini di Admin Controller
    public function download($id, $type)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pendaftaran = $this->pendaftaranModel->find($id);
        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Tentukan field dan folder berdasarkan type
        $config = [
            'cv' => ['field' => 'cv', 'folder' => 'cv', 'prefix' => 'CV_'],
            'surat' => ['field' => 'surat_pengantar', 'folder' => 'surat', 'prefix' => 'Surat_Pengantar_'],
            'ktm' => ['field' => 'ktm', 'folder' => 'ktm', 'prefix' => 'KTM_']
        ];

        if (!array_key_exists($type, $config)) {
            return redirect()->back()->with('error', 'Tipe file tidak valid.');
        }

        $field = $config[$type]['field'];
        $folder = $config[$type]['folder'];
        $prefix = $config[$type]['prefix'];

        // Cek apakah file ada
        if (empty($pendaftaran[$field])) {
            return redirect()->back()->with('error', 'File tidak tersedia.');
        }

        $fileName = $pendaftaran[$field];
        $filePath = WRITEPATH . 'uploads/' . $folder . '/' . $fileName;

        // Cek jika file tidak ada di WRITEPATH, coba FCPATH
        if (!file_exists($filePath)) {
            $filePath = FCPATH . 'uploads/' . $folder . '/' . $fileName;
        }

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan: ' . $fileName);
        }

        // Tentukan nama file untuk download
        $downloadName = $prefix . $pendaftaran['nama_lengkap'] . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

        return $this->response->download($filePath, null)->setFileName($downloadName);
    }
    // Method untuk proses interview
    public function processInterview($id, $action)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pendaftaran = $this->pendaftaranModel->find($id);
        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $catatan = $this->request->getGet('catatan') ?? '';
        $jadwal = $this->request->getGet('jadwal') ?? '';

        if ($action === 'lolos') {
            $nextStatus = $this->pendaftaranModel->getNextStatus($pendaftaran['status']);
            if (!$nextStatus) {
                return redirect()->back()->with('error', 'Tidak ada tahapan selanjutnya.');
            }

            $data = [
                'status' => $nextStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Set catatan untuk tahap sebelumnya
            if (!empty($catatan)) {
                $currentStep = $this->getInterviewStep($pendaftaran['status']);
                if ($currentStep > 0) {
                    $data['catatan_interview_' . $currentStep] = $catatan;
                }
            }

            // Set jadwal untuk tahap selanjutnya
            if (!empty($jadwal)) {
                $nextStep = $this->getInterviewStep($nextStatus);
                if ($nextStep > 0) {
                    $data['jadwal_interview_' . $nextStep] = date('Y-m-d H:i:s', strtotime($jadwal));
                }
            }

        } elseif ($action === 'tolak') {
            $rejectStatus = $this->pendaftaranModel->getRejectStatus($pendaftaran['status']);

            $data = [
                'status' => $rejectStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Set catatan penolakan
            if (!empty($catatan)) {
                $rejectStep = $this->getInterviewStep($rejectStatus);
                if ($rejectStep > 0) {
                    $data['catatan_interview_' . $rejectStep] = $catatan;
                } else {
                    $data['catatan_admin'] = $catatan;
                }
            }
        }

        if ($this->pendaftaranModel->update($id, $data)) {
            // Kirim email notifikasi
            $this->sendNotification($pendaftaran, $data['status'], $catatan, $jadwal);
            return redirect()->back()->with('success', 'Status berhasil diupdate!');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupdate status.');
        }
    }

    // Helper method untuk mendapatkan step interview dari status
    private function getInterviewStep($status)
    {
        if (preg_match('/Interview_(\d)/', $status, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }

    // Method untuk mengirim notifikasi
    private function sendNotification($pendaftaran, $status, $catatan = null, $jadwal = null)
    {
        // Implementasi pengiriman email/WhatsApp
        // Ini adalah template sederhana, Anda bisa integrasikan dengan email service atau WhatsApp API

        $statusMessages = [
            'Lolos_Interview_1' => [
                'subject' => 'Selamat! Anda Lolos Interview Tahap 1',
                'message' => "Selamat {$pendaftaran['nama_lengkap']}! Anda tahap interview 1." .
                    ($jadwal ? "\nJadwal Interview: " . date('d/m/Y H:i', strtotime($jadwal)) : "") .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ],
            'Lolos_Interview_2' => [
                'subject' => 'Selamat! Anda Lolos Interview Tahap 2',
                'message' => "Selamat {$pendaftaran['nama_lengkap']}! Anda tahap interview 2." .
                    ($jadwal ? "\nJadwal Interview: " . date('d/m/Y H:i', strtotime($jadwal)) : "") .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ],
            'Lolos_Interview_3' => [
                'subject' => 'Selamat! Anda Lolos Interview Tahap 3',
                'message' => "Selamat {$pendaftaran['nama_lengkap']}! Anda tahap interview 3." .
                    ($jadwal ? "\nJadwal Interview: " . date('d/m/Y H:i', strtotime($jadwal)) : "") .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ],
            'Lolos_Final' => [
                'subject' => 'Selamat! Anda Diterima Magang',
                'message' => "Selamat {$pendaftaran['nama_lengkap']}! Anda diterima untuk program magang. " .
                    "Tim kami akan menghubungi Anda via email/WhatsApp untuk informasi lebih lanjut."
            ],
            'Tidak_Lolos_Interview_1' => [
                'subject' => 'Hasil Interview Tahap 1',
                'message' => "Terima kasih {$pendaftaran['nama_lengkap']} telah mengikuti proses seleksi. " .
                    "Mohon maaf Anda tidak lolos." .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ],
            'Tidak_Lolos_Interview_2' => [
                'subject' => 'Hasil Interview Tahap 2',
                'message' => "Terima kasih {$pendaftaran['nama_lengkap']} telah mengikuti proses seleksi. " .
                    "Mohon maaf Anda tidak lolos." .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ],
            'Tidak_Lolos_Interview_3' => [
                'subject' => 'Hasil Interview Tahap 3',
                'message' => "Terima kasih {$pendaftaran['nama_lengkap']} telah mengikuti proses seleksi. " .
                    "Mohon maaf Anda tidak lolos." .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ],
            'Ditolak' => [
                'subject' => 'Hasil Pendaftaran Magang',
                'message' => "Terima kasih {$pendaftaran['nama_lengkap']} telah mendaftar program magang. " .
                    "Mohon maaf Anda tidak lolos." .
                    ($catatan ? "\nCatatan: {$catatan}" : "")
            ]
        ];

        if (isset($statusMessages[$status])) {
            $message = $statusMessages[$status];

            // PERBAIKAN: Gunakan null coalescing operator untuk email
            $email = $pendaftaran['email'] ?? 'tidak-ada-email@example.com';

            // Log notifikasi (untuk sementara)
            log_message('info', "Notifikasi untuk {$email}: {$message['subject']} - {$message['message']}");

            // Di sini Anda bisa implementasi:
            // - Kirim email menggunakan Email Library CodeIgniter
            // - Kirim WhatsApp menggunakan API
            // - Atau simpan di database untuk dikirim later

            // Contoh kirim email (uncomment jika sudah setup email)
            /*
            $emailService = \Config\Services::email();
            $emailService->setTo($email);
            $emailService->setSubject($message['subject']);
            $emailService->setMessage($message['message']);
            $emailService->send();
            */
        }
    }
    // Method for CV Analysis
    public function analyzeCv($id)
    {
        // Prevent timeout and suppress warning outputs to keep JSON clean
        set_time_limit(120);
        error_reporting(0);

        try {
            if (!session()->get('admin_logged_in')) {
                return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
            }

            $pendaftaran = $this->pendaftaranModel->find($id);
            if (!$pendaftaran || empty($pendaftaran['cv'])) {
                return $this->response->setJSON(['error' => 'Data or CV not found'])->setStatusCode(404);
            }

            $cvPath = WRITEPATH . 'uploads/cv/' . $pendaftaran['cv'];
            if (!file_exists($cvPath)) {
                $cvPath = FCPATH . 'uploads/cv/' . $pendaftaran['cv'];
            }

            if (!file_exists($cvPath)) {
                return $this->response->setJSON(['error' => 'CV File missing on server'])->setStatusCode(404);
            }

            $service = new \App\Services\CvAnalysisService();
            $result = $service->analyze($cvPath);

            return $this->response->setJSON($result);

        } catch (\Throwable $e) {
            log_message('error', '[AnalyzeCV Error] ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Server Error: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    // Toggle Registration Status
    public function toggleRegistration()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login');
        }

        $currentStatus = $this->settingsModel->getValue('registration_open') ?? '1';
        $newStatus = ($currentStatus == '1') ? '0' : '1';
        
        $this->settingsModel->setValue('registration_open', $newStatus);
        
        $message = ($newStatus == '1') ? 'Pendaftaran dibuka!' : 'Pendaftaran ditutup!';
        return redirect()->back()->with('success', $message);
    }
}
