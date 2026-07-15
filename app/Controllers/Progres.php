<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;
use CodeIgniter\API\ResponseTrait;

class Progres extends BaseController
{
    use ResponseTrait;

    protected $pendaftaranModel;

    public function __construct()
    {
        $this->pendaftaranModel = new PendaftaranModel();
        helper(['form', 'url', 'download']);
    }

    public function index()
    {
        $data = [
            'title' => 'Cek Progres Pendaftaran - Indosat Ooredoo Hutchison'
        ];

        return view('progres', $data);
    }

    /**
     * Cek status berdasarkan TOKEN pendaftaran
     */
    public function cek()
    {
        // Mengambil input token dari form
        $token = $this->request->getPost('token');

        if (empty(trim($token))) {
            return redirect()->back()->with('error', 'Token pendaftaran harus diisi');
        }

        // Cari data pendaftaran berdasarkan token_pendaftaran secara presisi
        $pendaftaran = $this->pendaftaranModel->where('token_pendaftaran', trim($token))->first();

        if ($pendaftaran) {
            $data = [
                'title'       => 'Hasil Pencarian - Indosat Ooredoo Hutchison',
                'pendaftaran' => $pendaftaran
            ];
            return view('progres', $data);
        } else {
            $data = [
                'title'      => 'Cek Progres Pendaftaran - Indosat Ooredoo Hutchison',
                'notFound'   => true,
                'searchTerm' => $token
            ];
            return view('progres', $data);
        }
    }

    /**
     * Fungsi Aman Download Berkas PDF (CV, Surat Pengantar, dll)
     * Mencegah eror path dan file corrupt
     */
    public function downloadPdf($jenis, $fileName)
    {
        // Validasi jenis dokumen untuk mencocokkan sub-folder penyimpanannya
        // disesuaikan dengan folder WRITEPATH di Pendaftaran Controller sebelumnya
        $subFolder = '';
        if ($jenis === 'cv') {
            $subFolder = 'cv';
        } elseif ($jenis === 'surat') {
            $subFolder = 'surat';
        } elseif ($jenis === 'ktm') {
            $subFolder = 'ktm';
        } else {
            return redirect()->back()->with('error', 'Jenis dokumen tidak valid.');
        }

        // Jalur path absolut file di folder writable
        $filePath = WRITEPATH . 'uploads/' . $subFolder . '/' . $fileName;

        // Cek fisik keberadaan file di storage server lokal
        if (file_exists($filePath) && !is_dir($filePath)) {
            // Menggunakan response helper CodeIgniter 4 untuk download paksa file mentah (.pdf)
            return $this->response->download($filePath, null)->setFileName($jenis . '-' . $fileName);
        } else {
            // Mengembalikan ke halaman sebelumnya jika file hilang/tidak ditemukan
            return redirect()->back()->with('error', 'File berkas tidak ditemukan di server.');
        }
    }

    public function cekStatus()
    {
        return $this->cek();
    }

   /**
     * Mengirimkan email berisi data lengkap, tombol cetak merah, dan lampiran berkas data resmi
     */
    /**
     * Mengirimkan email berisi tabel data lengkap, tombol cetak merah, dan ikon resmi
     */
    public function kirimEmail()
    {
        $token = $this->request->getPost('token_pendaftaran');
        $emailInput = $this->request->getPost('email');

        if (empty($token) || empty($emailInput)) {
            return redirect()->back()->with('error', 'Token dan Email tidak boleh kosong.');
        }

        // 1. Ambil data lengkap pendaftar dari database
        $pendaftaran = $this->pendaftaranModel->where('token_pendaftaran', $token)->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak valid.');
        }

        // Validasi kecocokan email input
        if (strtolower(trim($pendaftaran['email'])) !== strtolower(trim($emailInput))) {
            return redirect()->back()->with('error', 'Email yang Anda masukkan salah atau tidak terdaftar untuk token ini.');
        }

        // Helper label status pendaftaran
        $statusLabel = strtoupper($pendaftaran['status']);

        // ==========================================================================
        // 2. KONFIGURASI SMTP
        // ==========================================================================
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
        $emailService->setTo($pendaftaran['email']);
        $emailService->setSubject('📋 Data Lengkap Pendaftaran - ' . $pendaftaran['nama_lengkap']);
        
        // Link menuju halaman print PDF otomatis
        $linkCetak = base_url('progres/cetak-pdf/' . $pendaftaran['token_pendaftaran']);
        
        // URL Aset Gambar Resmi (Bukan Emoji / String biasa)
        $logoUrl = 'https://cdn-icons-png.flaticon.com/512/3135/3135665.png'; 
        $iconPdfUrl = 'https://cdn-icons-png.flaticon.com/512/337/337946.png'; // Gambar ikon berkas PDF resmi

        // ==========================================================================
        // 3. DESAIN TEMPLATE EMAIL: DATA DITAMPILKAN DALAM TABEL HTML RAPI
        // ==========================================================================
        $message = "
        <div style='background-color: #f4f6f9; padding: 30px 15px; font-family: Arial, sans-serif; color: #333;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;'>
                
                <!-- HEADER LOGO -->
                <tr>
                    <td align='center' style='background-color: #1e3a8a; padding: 30px 20px;'>
                        <img src='{$logoUrl}' alt='Logo FTP' style='width: 80px; height: auto; margin-bottom: 10px; display: block;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 600;'>Future Talent Program</h2>
                        <p style='color: #cbd5e1; margin: 5px 0 0 0; font-size: 14px;'>Detail Informasi Resmi Pendaftaran Magang</p>
                    </td>
                </tr>
                
                <!-- ISI EMAIL -->
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='font-size: 16px; line-height: 1.6; margin-top: 0;'>Halo <strong>" . esc($pendaftaran['nama_lengkap']) . "</strong>,</p>
                        <p style='font-size: 15px; line-height: 1.6; color: #555;'>Berikut adalah rincian data pendaftaran lengkap Anda yang telah tersimpan secara resmi di dalam basis data sistem kami:</p>
                        
                        <!-- TABEL DATA FORMATED HTML -->
                        <table style='width: 100%; border-collapse: collapse; margin: 25px 0; font-size: 14px;'>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; width: 40%; border-bottom: 1px solid #edf2f7; color: #475569;'>Token Pendaftaran</td>
                                <td style='padding: 10px 0; color: #1e3a8a; font-weight: bold; border-bottom: 1px solid #edf2f7; font-family: monospace; font-size: 16px;'>" . esc($pendaftaran['token_pendaftaran']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Nama Lengkap</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7; font-weight: bold;'>" . esc($pendaftaran['nama_lengkap']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Email Terdaftar</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . esc($pendaftaran['email']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Nomor WhatsApp</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . esc($pendaftaran['nomor_whatsapp']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Asal Kampus / Sekolah</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . esc($pendaftaran['asal_kampus']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Program Studi</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . esc($pendaftaran['program_studi']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Divisi Pilihan</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7; color: #1e3a8a; font-weight: bold;'>" . esc($pendaftaran['divisi_pilihan']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Semester</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . esc($pendaftaran['semester']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Jenis Magang</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . ucfirst(esc($pendaftaran['jenis_magang'])) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Periode Magang</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . ($pendaftaran['periode_mulai'] ? date('d M Y', strtotime($pendaftaran['periode_mulai'])) : '-') . " s/d " . ($pendaftaran['periode_selesai'] ? date('d M Y', strtotime($pendaftaran['periode_selesai'])) : '-') . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Tanggal Mendaftar</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'>" . date('d F Y H:i', strtotime($pendaftaran['created_at'])) . " WIB</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; border-bottom: 1px solid #edf2f7; color: #475569;'>Status Seleksi</td>
                                <td style='padding: 10px 0; border-bottom: 1px solid #edf2f7;'><span style='background-color: #fef08a; color: #854d0e; padding: 4px 10px; border-radius: 4px; font-weight: bold; display: inline-block; font-size: 12px; border: 1px solid #fef08a;'>{$statusLabel}</span></td>
                            </tr>
                        </table>

                        <!-- TOMBOL MERAH UNTUK CETAK PDF DENGAN IKON GAMBAR ASLI -->
                        <div style='text-align: center; margin: 35px 0;'>
                            <a href='{$linkCetak}' target='_blank' style='background-color: #dc2626; color: #ffffff; padding: 14px 28px; text-decoration: none; font-size: 15px; font-weight: bold; border-radius: 5px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #b91c1c;'>
                                <img src='{$iconPdfUrl}' alt='PDF Icon' style='width: 18px; height: 18px; margin-right: 8px; vertical-align: middle; display: inline-block;'>
                                <span style='vertical-align: middle;'>CETAK / DOWNLOAD PDF BERKAS</span>
                            </a>
                        </div>
                    </td>
                </tr>
                
                <!-- FOOTER EMAIL -->
                <tr>
                    <td align='center' style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #edf2f7; font-size: 12px; color: #999;'>
                        <p style='margin: 0;'>&copy; " . date('Y') . " Future Talent Program. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </div>
        ";

        $emailService->setMessage($message);

        // 4. Eksekusi pengiriman email
        if ($emailService->send()) {
            return redirect()->to(base_url('progres'))->with('success', 'Data lengkap pendaftaran berhasil dikirim ke email: ' . $emailInput);
        } else {
            log_message('error', $emailService->printDebugger(['headers', 'subject', 'body']));
            return redirect()->back()->with('error', 'Gagal mengirimkan email data.');
        }
    }
    /**
     * Menampilkan halaman manifes data resmi siap cetak ke PDF secara native
     */
    public function cetakPdf($token = null)
    {
        if (!$token) {
            return "Token tidak valid.";
        }

        $pendaftaran = $this->pendaftaranModel->where('token_pendaftaran', $token)->first();

        if (!$pendaftaran) {
            return "Data pendaftaran tidak ditemukan.";
        }

        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <title>Data_Pendaftaran_" . esc($pendaftaran['token_pendaftaran']) . "</title>
            <style>
                body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 40px; margin: 0; background-color: #fff; }
                .container { max-width: 700px; margin: 0 auto; border: 1px solid #ddd; padding: 40px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
                .header { text-align: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 30px; }
                .title { font-size: 22px; font-weight: bold; color: #1e3a8a; margin: 0; letter-spacing: 1px; }
                .subtitle { font-size: 13px; color: #666; margin: 5px 0 0 0; text-transform: uppercase; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eaebd7; font-size: 14px; }
                th { background-color: #f8fafc; width: 35%; font-weight: bold; color: #475569; border-right: 1px solid #eee; }
                .status-box { background-color: #fef08a; color: #854d0e; padding: 4px 10px; border-radius: 4px; font-weight: bold; display: inline-block; font-size: 12px; border: 1px solid #ca8a04; }
                .footer { text-align: center; margin-top: 60px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
                
                @media print {
                    body { padding: 0; }
                    .container { border: none; box-shadow: none; padding: 0; max-width: 100%; }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='title'>FUTURE TALENT PROGRAM</div>
                    <div class='subtitle'>Manifes Resmi & Detail Data Pendaftaran Magang</div>
                </div>
                
                <p style='font-size: 14px;'>Dokumen cetak ini valid memuat data pendaftaran yang tersimpan di dalam basis data server rekrutmen:</p>
                
                <table>
                    <tr>
                        <th>Token Pendaftaran</th>
                        <td style='font-family: monospace; font-size: 16px; font-weight: bold; color: #1e3a8a;'>" . esc($pendaftaran['token_pendaftaran']) . "</td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>" . esc($pendaftaran['nama_lengkap']) . "</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>" . esc($pendaftaran['email']) . "</td>
                    </tr>
                    <tr>
                        <th>Nomor WhatsApp</th>
                        <td>" . esc($pendaftaran['nomor_whatsapp']) . "</td>
                    </tr>
                    <tr>
                        <th>Asal Kampus / Sekolah</th>
                        <td>" . esc($pendaftaran['asal_kampus']) . "</td>
                    </tr>
                    <tr>
                        <th>Program Studi / Jurusan</th>
                        <td>" . esc($pendaftaran['program_studi']) . "</td>
                    </tr>
                    <tr>
                        <th>Divisi Pilihan</th>
                        <td style='font-weight: bold; color: #1e3a8a;'>" . esc($pendaftaran['divisi_pilihan']) . "</td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td>" . esc($pendaftaran['semester']) . "</td>
                    </tr>
                    <tr>
                        <th>Jenis Magang</th>
                        <td>" . ucfirst(esc($pendaftaran['jenis_magang'])) . "</td>
                    </tr>
                    <tr>
                        <th>Periode Magang</th>
                        <td>" . ($pendaftaran['periode_mulai'] ? date('d M Y', strtotime($pendaftaran['periode_mulai'])) : '-') . " s/d " . ($pendaftaran['periode_selesai'] ? date('d M Y', strtotime($pendaftaran['periode_selesai'])) : '-') . "</td>
                    </tr>
                    <tr>
                        <th>Tanggal Mendaftar</th>
                        <td>" . date('d F Y H:i', strtotime($pendaftaran['created_at'])) . " WIB</td>
                    </tr>
                    <tr>
                        <th>Status Seleksi</th>
                        <td><div class='status-box'>" . strtoupper($pendaftaran['status']) . "</div></td>
                    </tr>
                </table>
                
                <div class='footer'>
                    <p>Dicetak pada: " . date('d-m-Y H:i:s') . " WIB - Hak Cipta Dilindungi Sistem Rekrutmen FTP.</p>
                    <p>Dokumen digital ini sah berkekuatan hukum sistem dan tidak memerlukan tanda tangan basah.</p>
                </div>
            </div>

            <script>
                window.onload = function() {
                    window.print();
                };
            </script>
        </body>
        </html>
        ";
    }
    
}