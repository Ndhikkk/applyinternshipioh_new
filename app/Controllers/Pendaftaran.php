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
            'nama_lengkap'   => 'required|trim',
            'email'          => [
                'rules' => 'required|valid_email'
            ],
            'nomor_whatsapp' => [
                'rules' => 'required|numeric|min_length[11]',
                'errors' => [
                    'numeric'   => 'Nomor WhatsApp hanya boleh berisi angka tanpa spasi atau simbol.',
                    'min_length'=> 'Nomor WhatsApp harus terdiri dari minimal 11 digit angka.'
                ]
            ],
            'nomor_darurat'  => [
                'rules' => 'required|trim|numeric|min_length[11]|differs[nomor_whatsapp]',
                'errors' => [
                    'numeric'    => 'Nomor Darurat hanya boleh berisi angka tanpa spasi atau simbol.',
                    'min_length' => 'Nomor Darurat harus terdiri dari minimal 11 digit angka.',
                    'differs'    => 'Nomor Darurat harus berbeda dengan Nomor WhatsApp Anda.'
                ]
            ],
            'asal_kampus'    => 'required|trim',
            'program_studi'  => 'required|trim',
            'kota_pilihan'   => 'required|in_list[Semarang,Surabaya,Bali]',
            'divisi_pilihan' => 'required|trim',
            'semester'       => 'required|integer',
            'jenis_magang'   => 'required|in_list[Wajib,Mandiri]',
            'periode_mulai'  => 'required',
            'periode_selesai'=> 'required',
            'cv'             => 'uploaded[cv]|max_size[cv,2048]|ext_in[cv,pdf]|mime_in[cv,application/pdf]',
            'surat_pengantar'=> 'permit_empty|max_size[surat_pengantar,2048]|ext_in[surat_pengantar,pdf]|mime_in[surat_pengantar,application/pdf]',
            'proposal_magang'=> 'permit_empty|max_size[proposal_magang,2048]|ext_in[proposal_magang,pdf]|mime_in[proposal_magang,application/pdf]',
            'ktm'            => 'permit_empty|max_size[ktm,4096]|ext_in[ktm,pdf,jpg,jpeg,png]|mime_in[ktm,application/pdf,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // This check gives a useful response for ordinary repeat submissions.
        // The unique database indexes added by the migration are still required
        // to handle two requests that arrive at exactly the same time.
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $nomorWhatsapp = trim((string) $this->request->getPost('nomor_whatsapp'));
        $requestId = bin2hex(random_bytes(6));
        log_message('info', 'Registration POST received. request_id={requestId}', ['requestId' => $requestId]);
        $existing = $this->findExistingRegistration($email, $nomorWhatsapp);

        if ($existing !== null) {
            log_message(
                'info',
                'Registration POST reused an existing registration. request_id={requestId}, registration_id={registrationId}',
                ['requestId' => $requestId, 'registrationId' => $existing['id']]
            );

            // A retry must be safe. It may be a browser retry, a slow-network
            // retry, or the same form submitted twice; in every case, show the
            // original successful registration rather than a false error.
            return redirect()->to(site_url('pendaftaran/success'))->with('registration', $existing);
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

        $proposal_magang = $this->request->getFile('proposal_magang');
        $proposalName = '';
        if ($proposal_magang && $proposal_magang->isValid() && !$proposal_magang->hasMoved()) {
            $proposalName = $proposal_magang->getRandomName();
            $proposal_magang->move(WRITEPATH . 'uploads/proposal', $proposalName);
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
            'nama_lengkap'    => trim((string)$this->request->getPost('nama_lengkap')),
            'email'           => $email,
            'nomor_whatsapp'  => $nomorWhatsapp,
            'nomor_darurat'   => trim((string)$this->request->getPost('nomor_darurat')),
            'asal_kampus'     => trim((string)$this->request->getPost('asal_kampus')),
            'program_studi'   => trim((string)$this->request->getPost('program_studi')),
            'kota_pilihan'    => $this->request->getPost('kota_pilihan'),
            'divisi_pilihan'  => trim((string)$this->request->getPost('divisi_pilihan')),
            'semester'        => $this->request->getPost('semester'),
            'jenis_magang'    => $this->request->getPost('jenis_magang'),
            'periode_mulai'   => $this->request->getPost('periode_mulai'),
            'periode_selesai' => $this->request->getPost('periode_selesai'),
            'cv'              => $cvName, 
            'surat_pengantar' => $suratName,
            'proposal_magang' => $proposalName,
            'ktm'             => $ktmName,
            'status'          => 'Menunggu',
            'catatan'         => '',
        ];

        $insertId = $this->pendaftaranModel->insert($data);

        if ($insertId === false) {
            $this->deleteUploadedFiles([$cvName, $suratName, $proposalName, $ktmName], ['cv', 'surat', 'proposal', 'ktm']);

            // Another request can insert the same applicant between the
            // pre-check above and this INSERT. Treat that request as the same
            // registration, rather than showing an error after it succeeded.
            $existing = $this->findExistingRegistration($email, $nomorWhatsapp);
            if ($existing !== null) {
                log_message(
                    'info',
                    'Registration INSERT raced with another request and reused its row. request_id={requestId}, registration_id={registrationId}',
                    ['requestId' => $requestId, 'registrationId' => $existing['id']]
                );
                return redirect()->to(site_url('pendaftaran/success'))->with('registration', $existing);
            }

            return redirect()->back()->withInput()->with(
                'error',
                'Pendaftaran tidak dapat disimpan. Silakan coba lagi.'
            );
        }

        $newData  = $this->pendaftaranModel->find($insertId);
        log_message(
            'info',
            'Registration inserted. request_id={requestId}, registration_id={registrationId}',
            ['requestId' => $requestId, 'registrationId' => $insertId]
        );

        // Panggil fungsi kirim email resmi
        $this->sendEmailToken($newData['email'], $newData['nama_lengkap'], $token);

        // Post/Redirect/Get prevents a browser refresh from posting the same
        // multipart form again after a successful registration.
        return redirect()->to(site_url('pendaftaran/success'))->with('registration', $newData);
    }

    public function success()
    {
        $data = session()->getFlashdata('registration');

        if (!is_array($data)) {
            return redirect()->to(site_url('pendaftaran'));
        }

        return view('pendaftaran_success', ['data' => $data]);
    }

    public function import_cv()
    {
        $nama_lengkap   = $this->request->getPost('nama_lengkap');
        $email          = strtolower(trim((string) $this->request->getPost('email')));
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

        // Cek duplikasi email & whatsapp
        $existing = $this->pendaftaranModel
            ->where('email', strip_tags(trim($email)))
            ->orWhere('nomor_whatsapp', strip_tags(trim($nomor_whatsapp)))
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Gagal! Email atau Nomor WhatsApp pelamar ini sudah pernah terdaftar sebelumnya.');
        }

        $cvName    = $this->handleFileUpload('file_cv', 'cv');
        $suratName = $this->handleFileUpload('file_surat', 'surat');

        if ($cvName === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal! Dokumen CV yang diunggah tidak valid atau tidak terbaca.');
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
                'message' => 'Gagal menyimpan data ekstraksi. Email atau Nomor WhatsApp mungkin sudah terdaftar.'
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

    private function deleteUploadedFiles(array $fileNames, array $folders): void
    {
        foreach ($fileNames as $index => $fileName) {
            if ($fileName === '' || !isset($folders[$index])) {
                continue;
            }

            $path = WRITEPATH . 'uploads/' . $folders[$index] . '/' . $fileName;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function findExistingRegistration(string $email, string $nomorWhatsapp): ?array
    {
        return $this->pendaftaranModel
            ->groupStart()
                ->where('email', $email)
                ->orWhere('nomor_whatsapp', $nomorWhatsapp)
            ->groupEnd()
            ->first();
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
        $config->SMTPUser   = 'nnusa0001@gmail.com';
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
            log_message('error', 'Registration token email could not be sent to {email}.', ['email' => $toEmail]);
            return false;
        }

        return true;
    }
   
}
