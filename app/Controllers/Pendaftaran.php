<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;
use App\Models\AppSettingsModel;
use CodeIgniter\Controller;

class Pendaftaran extends Controller
{
    protected $pendaftaranModel;

    public function __construct()
    {
        $this->pendaftaranModel = new PendaftaranModel();
    }

    public function index()
    {
        $settingsModel = new AppSettingsModel();
        $data['registration_open'] = $settingsModel->getValue('registration_open') ?? '1';
        
        // Contoh daftar divisi (bisa juga Anda ambil dari database jika ada tabel divisi)
        $data['divisi_list'] = ['Web Developer', 'Mobile Developer', 'UI/UX Designer', 'Digital Marketing', 'Content Writer'];
        
        return view('pendaftaran', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama_lengkap'   => 'required',
            'email'          => 'required|valid_email',
            'nomor_whatsapp' => 'required',
            'asal_kampus'    => 'required',
            'program_studi'  => 'required',
            'divisi_pilihan' => 'required',
            'semester'       => 'required|integer',
            'jenis_magang'   => 'required|in_list[Wajib,Mandiri]',
            'periode_mulai'  => 'required',
            'periode_selesai'=> 'required',
            'cv'             => 'uploaded[cv]|max_size[cv,2048]|ext_in[cv,pdf]|mime_in[cv,application/pdf]',
            'surat_pengantar'=> 'permit_empty|max_size[surat_pengantar,2048]|ext_in[surat_pengantar,pdf]|mime_in[surat_pengantar,application/pdf]',
            'ktm'            => 'permit_empty|max_size[ktm,4096]|ext_in[ktm,pdf,jpg,jpeg,png]|mime_in[ktm,application/pdf,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Upload file ke Local Server
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

        // GENERATE TOKEN
        $token = $this->generateToken();

        $data = [
            'token_pendaftaran' => $token,
            'nama_lengkap'    => $this->request->getPost('nama_lengkap'),
            'email'           => $this->request->getPost('email'),
            'nomor_whatsapp'  => $this->request->getPost('nomor_whatsapp'),
            'asal_kampus'     => $this->request->getPost('asal_kampus'),
            'program_studi'   => $this->request->getPost('program_studi'),
            'divisi_pilihan'  => $this->request->getPost('divisi_pilihan'),
            'semester'        => $this->request->getPost('semester'),
            'jenis_magang'    => $this->request->getPost('jenis_magang'),
            'periode_mulai'   => $this->request->getPost('periode_mulai'),
            'periode_selesai' => $this->request->getPost('periode_selesai'),
            'cv'              => $cvName, 
            'surat_pengantar' => $suratName,
            'ktm'             => $ktmName,
            'status'          => 'Menunggu',
            'catatan'         => '',
        ];

        $insertId = $this->pendaftaranModel->insert($data);
        $newData  = $this->pendaftaranModel->find($insertId);

        // Panggil fungsi kirim email resmi
        $this->sendEmailToken($newData['email'], $newData['nama_lengkap'], $token);

        return view('pendaftaran_success', ['data' => $newData]);
    }

    public function import_cv()
    {
        $nama_lengkap   = $this->request->getPost('nama_lengkap');
        $email          = $this->request->getPost('email');
        $nomor_whatsapp = $this->request->getPost('nomor_whatsapp');
        $asal_kampus    = $this->request->getPost('asal_kampus'); 
        $program_studi  = $this->request->getPost('program_studi');
        $semester       = $this->request->getPost('semester');
        $jenis_magang   = $this->request->getPost('jenis_magang');
        $divisi_pilihan = $this->request->getPost('divisi_pilihan');
        $batch_id       = $this->request->getPost('batch_id');

        // Validasi input nama
        if (empty($nama_lengkap)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan! Nama pelamar tidak boleh kosong.'
            ]);
        }

        $cvName    = $this->handleFileUpload('file_cv', 'cv');
        $suratName = $this->handleFileUpload('file_surat', 'surat');

        if ($cvName === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal! Dokumen CV yang diunggah tidak valid atau tidak terbaca.'
            ]);
        }

        // GENERATE TOKEN (Maksimal 10 Karakter)
        $token = $this->generateToken();

        $insertData = [
            'token_pendaftaran' => $token,
            'nama_lengkap'    => strip_tags(trim($nama_lengkap)),
            'email'           => strip_tags(trim($email)),
            'nomor_whatsapp'  => strip_tags(trim($nomor_whatsapp)),
            'asal_kampus'     => strip_tags(trim($asal_kampus)),
            'program_studi'   => strip_tags(trim($program_studi)),
            'semester'        => !empty($semester) ? (int)$semester : 1,
            'jenis_magang'    => $jenis_magang ?? 'Mandiri',
            'divisi_pilihan'  => $divisi_pilihan ?? '',
            'periode_mulai'   => date('Y-m-d'), 
            'periode_selesai' => date('Y-m-d', strtotime('+3 months')), 
            'cv'              => $cvName ?? '',
            'surat_pengantar' => $suratName ?? '',
            'ktm'             => '', 
            'status'          => 'Menunggu',
            'catatan'         => 'Dibuat otomatis via Scan OCR',
            'import_source'   => 'scan_ai',
            'referral_token'  => bin2hex(random_bytes(16)),
            'batch_id'        => !empty($batch_id) ? $batch_id : null,
        ];

        if ($this->pendaftaranModel->insert($insertData)) {
            // Kirim email token untuk pendaftaran via OCR
            $this->sendEmailToken($insertData['email'], $insertData['nama_lengkap'], $token);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Berhasil mengurai CV! Data pelamar atas nama "' . esc($nama_lengkap) . '" telah tersimpan.'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data ekstraksi ke database.'
            ]);
        }
    }

    private function handleFileUpload($fieldName, $folderName)
    {
        $file = $this->request->getFile($fieldName);
        
        if (!$file || !$file->isValid()) {
            return ($fieldName === 'file_cv') ? false : '';
        }

        if (!$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/' . $folderName, $newName);
            return $newName;
        }

        return '';
    }

    /**
     * Helper untuk Generate Token Maksimal 10 Karakter
     * Format: YYMMDD + Urutan Hari Ini (1-2 digit) + Acak sisa ruang kosong (total pas 10)
     */
    private function generateToken()
    {
        $today = date('Y-m-d');
        $prefix = date('ymd'); // Contoh: 260714 (6 digit)

        // Hitung berapa pendaftar hari ini di database
        $orderToday = $this->pendaftaranModel->where('DATE(created_at)', $today)->countAllResults();
        $sequence = $orderToday + 1; // Urutan pendaftaran berikutnya

        // Sisa ruang karakter maksimal yang bisa kita isi (10 - 6 digit prefix = 4 digit)
        $remainingLength = 10 - strlen($prefix);

        // Siapkan string sequence (misal: "1", "12", "135")
        $seqString = (string) $sequence;
        $seqLength = strlen($seqString);

        // Jika panjang urutan melebihi sisa ruang, pangkas agar pas 10 karakter total
        if ($seqLength >= $remainingLength) {
            $token = $prefix . substr($seqString, -$remainingLength);
        } else {
            // Jika urutan masih menyisakan ruang kosong, isi sisa ruang tersebut dengan angka acak
            $randomLength = $remainingLength - $seqLength;
            
            // Generate angka acak sesuai sisa digit yang dibutuhkan
            $min = pow(10, $randomLength - 1);
            $max = pow(10, $randomLength) - 1;
            $randomNumber = rand($min, $max);

            $token = $prefix . $seqString . $randomNumber;
        }

        return $token;
    }

  /**
     * Fungsi Kirim Email Token dengan Tampilan Menarik & Logo
     */
    private function sendEmailToken($toEmail, $recipientName, $token)
    {
        // 1. Inisialisasi konfigurasi SMTP
        $config = new \Config\Email();
        $config->protocol   = 'smtp';
        $config->SMTPHost   = 'smtp.gmail.com';
        $config->SMTPUser   = 'farezaairo@gmail.com';
        $config->SMTPPass   = 'xerd xhmk bzpp rmbs'; 
        $config->SMTPPort   = 587;
        $config->SMTPCrypto = 'tls';
        $config->mailType   = 'html';
        $config->charset    = 'utf-8';
        $config->CRLF       = "\r\n";
        $config->newline    = "\r\n";

        $emailService = \Config\Services::email($config);

        $emailService->setFrom('farezaairo@gmail.com', 'Future Talent Program');
        $emailService->setTo($toEmail);
        $emailService->setSubject('🔑 Token Pendaftaran - Future Talent Program');
        
       
        $logoUrl = 'https://cdn-icons-png.flaticon.com/512/3135/3135665.png'; 

        // 2. Desain Template Email HTML (Responsive Card Layout)
        $message = "
        <div style='background-color: #f4f6f9; padding: 30px 15px; font-family: Arial, sans-serif; color: #333;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;'>
                
                <!-- HEADER LOGO -->
                <tr>
                    <td align='center' style='background-color: #1e3a8a; padding: 30px 20px;'>
                        <img src='{$logoUrl}' alt='Logo FTP' style='width: 80px; height: auto; margin-bottom: 10px; display: block;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;'>Future Talent Program</h2>
                    </td>
                </tr>
                
                <!-- KONTEN UTAMA -->
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='font-size: 16px; line-height: 1.6; margin-top: 0;'>Halo <strong>{$recipientName}</strong>,</p>
                        <p style='font-size: 15px; line-height: 1.6; color: #555;'>Terima kasih telah mendaftar dalam program <strong>Future Talent Program</strong>. Kami sangat mengapresiasi minat dan antusiasme Anda untuk bertumbuh bersama kami.</p>
                        
                        <!-- KOTAK TOKEN -->
                        <div style='background-color: #f0f4f8; border-left: 4px solid #1e3a8a; border-radius: 4px; padding: 20px; margin: 30px 0; text-align: center;'>
                            <span style='font-size: 13px; text-transform: uppercase; tracking: 1px; color: #666; display: block; margin-bottom: 5px;'>Nomor Token Anda</span>
                            <span style='font-size: 26px; font-weight: bold; color: #1e3a8a; letter-spacing: 3px; font-family: monospace;'>{$token}</span>
                        </div>
                        
                        <p style='font-size: 14px; line-height: 1.6; color: #666;'>Simpan dan gunakan token di atas untuk melacak status seleksi berkas Anda melalui dashboard pendaftaran di masa mendatang.</p>
                        
                        <!-- TOMBOL AKSI -->
                        <div style='text-align: center; margin-top: 35px;'>
                            <a href='http://localhost:8080/pendaftaran' style='background-color: #1e3a8a; color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 15px; font-weight: bold; border-radius: 5px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>Cek Status Pendaftaran</a>
                        </div>
                    </td>
                </tr>
                
                <!-- FOOTER -->
                <tr>
                    <td align='center' style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #edf2f7; font-size: 12px; color: #999;'>
                        <p style='margin: 0 0 5px 0;'>Email ini dikirim otomatis oleh sistem rekrutmen FTP.</p>
                        <p style='margin: 0;'>&copy; " . date('Y') . " Future Talent Program. All rights reserved.</p>
                    </td>
                </tr>
                
            </table>
        </div>
        ";

        $emailService->setMessage($message);

        // 3. Eksekusi pengiriman
        if (!$emailService->send()) {
            echo "<h3>Gagal Mengirim Email! Log Error:</h3>";
            echo $emailService->printDebugger(['headers', 'subject', 'body']);
            exit;
        }
    }
   
}