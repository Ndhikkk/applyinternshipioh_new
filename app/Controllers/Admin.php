<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\PendaftaranModel;
use App\Models\AppSettingsModel;
use Config\Services;

class Admin extends BaseController
{
    protected $adminModel;
    protected $pendaftaranModel;
    protected $settingsModel;

    private const MAX_INTERVIEW_STEP = 3;

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
        if (session()->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin/login');
    }

    public function loginProcess()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password harus diisi.');
        }

        $admin = $this->adminModel->where('username', $username)->first();

        if ($admin && password_verify($password, $admin['password'])) {
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
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Hapus otomatis data yang sudah lewat tenggat waktu retensi
        // (masuk arsip dulu 7 hari, baru dihapus permanen kalau tidak dipulihkan)
        $this->runRetentionCleanup();

        $isArsip = $this->request->getGet('arsip') == '1';

        $data['total_pendaftar'] = $this->pendaftaranModel->where('is_archived', 0)->countAll();

        $data['total_diterima'] = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->groupStart()
                ->where('status', 'Diterima')
                ->orWhere('status', 'Lolos_Final')
            ->groupEnd()
            ->countAllResults();

        $data['total_ditolak'] = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->groupStart()
                ->where('status', 'Ditolak')
                ->orWhere('status', 'Tidak_Lolos_Interview_1')
                ->orWhere('status', 'Tidak_Lolos_Interview_2')
                ->orWhere('status', 'Tidak_Lolos_Interview_3')
            ->groupEnd()
            ->countAllResults();

        $data['total_menunggu'] = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->whereNotIn('status', [
                'Diterima', 'Lolos_Final', 'Ditolak',
                'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'
            ])
            ->countAllResults();

        $data['total_arsip'] = $this->pendaftaranModel->where('is_archived', 1)->countAllResults();

        $keyword = $this->request->getGet('keyword');
        $modelQuery = $this->pendaftaranModel->where('is_archived', $isArsip ? 1 : 0);

        if (!empty($keyword)) {
            $modelQuery = $modelQuery->groupStart()
                ->like('nama_lengkap', $keyword)
                ->orLike('email', $keyword)
                ->orLike('asal_kampus', $keyword)
                ->orLike('program_studi', $keyword)
                ->orLike('nomor_whatsapp', $keyword)
                ->orLike('token_pendaftaran', $keyword)
                ->orLike('status', $keyword)
            ->groupEnd();
        }

        $sortField = $isArsip ? 'archived_at' : 'created_at';
        $data['pendaftaran'] = $modelQuery->orderBy($sortField, 'DESC')->paginate(15, 'pendaftaran');
        $data['pager'] = $this->pendaftaranModel->pager;
        $data['keyword'] = $keyword;
        $data['is_arsip'] = $isArsip;
        $data['registration_open'] = $this->settingsModel->getValue('registration_open') ?? '1';

        return view('admin/dashboard', $data);
    }

    /**
     * Update status manual sederhana (dipakai form di halaman detail.php).
     */
    public function updateStatus($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $status  = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan') ?? $this->request->getPost('catatan_admin');

        if (!in_array($status, PendaftaranModel::statusList(), true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $data = ['status' => $status, 'status_changed_at' => date('Y-m-d H:i:s')];
        if ($catatan !== null) {
            $data['catatan_admin'] = $catatan;
        }

        if ($this->pendaftaranModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Status pendaftaran berhasil diupdate!');
        }

        return redirect()->back()->with('error', 'Gagal mengupdate status: ' . implode(', ', $this->pendaftaranModel->errors()));
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
        $filename = 'data_pendaftaran_magang_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No', 'Token', 'Nama', 'Email', 'WhatsApp', 'Kampus', 'Program Studi', 'Divisi', 'Semester', 'Jenis Magang', 'Status', 'Periode Magang', 'Tanggal Daftar']);

        $no = 1;
        foreach ($pendaftaran as $row) {
            fputcsv($output, [
                $no++,
                $row['token_pendaftaran'] ?? '-',
                $row['nama_lengkap'],
                $row['email'] ?? '-',
                $row['nomor_whatsapp'],
                $row['asal_kampus'],
                $row['program_studi'],
                $row['divisi_pilihan'] ?? '-',
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
            return $this->ajaxOr($id, false, 'Silakan login terlebih dahulu.', 401, fn () => redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.'));
        }

        $pendaftaran = $this->pendaftaranModel->find($id);

        if ($pendaftaran) {
            $this->deleteCandidateFiles($pendaftaran);
            $this->pendaftaranModel->delete($id);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'id' => (int) $id, 'message' => 'Data pendaftaran berhasil dihapus dari sistem.']);
            }
            return redirect()->to('/admin/dashboard')->with('success', 'Data pendaftaran berhasil dihapus dari sistem.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
        }
        return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('success', 'Logout berhasil!');
    }

    public function download($id, $type)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pendaftaran = $this->pendaftaranModel->find($id);
        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

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

        if (empty($pendaftaran[$field])) {
            return redirect()->back()->with('error', 'File tidak tersedia.');
        }

        $fileName = $pendaftaran[$field];
        $filePath = WRITEPATH . 'uploads/' . $folder . '/' . $fileName;

        if (!file_exists($filePath)) {
            $filePath = FCPATH . 'uploads/' . $folder . '/' . $fileName;
        }

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File fisik tidak ditemukan pada server.');
        }

        $downloadName = $prefix . $pendaftaran['nama_lengkap'] . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
        return $this->response->download($filePath, null)->setFileName($downloadName);
    }

    /**
     * SATU endpoint serba-bisa, sengaja dibuat pakai route yang SUDAH ADA
     * (admin/process-interview/(:num)/(:segment)) supaya TIDAK perlu edit
     * Routes.php sama sekali. Dibedakan lewat isi $action:
     *   - 'info'   -> GET data lengkap 1 kandidat (buat isi modal aksi)
     *   - 'wa'     -> GET link WhatsApp siap kirim
     *   - 'email'  -> kirim email notifikasi sesuai status saat ini
     *   - selain itu -> dianggap NAMA STATUS TUJUAN (mis. 'Lolos_Interview_2',
     *                   'Diterima', 'Ditolak', 'Menunggu') dan akan mengubah
     *                   status kandidat.
     */
    public function processInterview($id, $action)
    {
        if (!session()->get('admin_logged_in')) {
            return $this->jsonOrRedirect(false, 'Silakan login terlebih dahulu.', 401);
        }

        $pendaftaran = $this->pendaftaranModel->find($id);
        if (!$pendaftaran) {
            return $this->jsonOrRedirect(false, 'Data tidak ditemukan.', 404);
        }

        $action = urldecode($action);

        if ($action === 'info') {
            return $this->response->setJSON(array_merge(['success' => true], $this->itemPayload($pendaftaran)));
        }

        if ($action === 'wa') {
            $tpl = $this->buildWaTemplate($pendaftaran);
            if (!$tpl['url']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nomor WhatsApp kandidat tidak valid/kosong.']);
            }
            return $this->response->setJSON(['success' => true, 'url' => $tpl['url'], 'message' => $tpl['message']]);
        }

        if ($action === 'email') {
            if (empty($pendaftaran['email'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Kandidat tidak memiliki alamat email.']);
            }
            $result = $this->sendStatusEmail($pendaftaran);
            $this->pendaftaranModel->update($id, ['email_terkirim' => $result['sent'] ? 1 : 0]);
            return $this->response->setJSON([
                'success' => $result['sent'],
                'message' => $result['sent']
                    ? 'Email notifikasi berhasil dikirim ke ' . $pendaftaran['email']
                    : 'Gagal mengirim email: ' . $result['error'],
            ]);
        }

        if ($action === 'restore') {
            $this->pendaftaranModel->update($id, [
                'is_archived'     => 0,
                'archived_at'     => null,
                'archived_reason' => null,
            ]);
            return $this->response->setJSON(['success' => true, 'id' => (int) $id, 'message' => 'Data berhasil dipulihkan dari arsip.']);
        }

        // Selain itu: $action adalah target status baru
        return $this->handleSetStatus($id, $pendaftaran, $action);
    }

    private function handleSetStatus($id, array $pendaftaran, string $targetStatus)
    {
        if (!in_array($targetStatus, PendaftaranModel::statusList(), true)) {
            return $this->jsonOrRedirect(false, 'Status tujuan tidak dikenali: ' . $targetStatus, 422);
        }

        $catatan  = trim((string) ($this->request->getGet('catatan') ?? $this->request->getPost('catatan') ?? ''));
        $jadwal   = trim((string) ($this->request->getGet('jadwal') ?? $this->request->getPost('jadwal') ?? ''));
        $linkZoom = trim((string) ($this->request->getGet('link_zoom') ?? $this->request->getPost('link_zoom') ?? ''));

        $data = ['status' => $targetStatus];
        if ($targetStatus !== $pendaftaran['status']) {
            $data['status_changed_at'] = date('Y-m-d H:i:s');
        }
        $step = $this->getInterviewStep($targetStatus);

        if ($step > 0) {
            // Lolos_Interview_N atau Tidak_Lolos_Interview_N
            if ($catatan !== '') {
                $data['catatan_interview_' . $step] = $catatan;
            }
            if (str_starts_with($targetStatus, 'Lolos_Interview_')) {
                if ($jadwal !== '') {
                    $parsed = $this->parseJadwal($jadwal);
                    if ($parsed === null) {
                        return $this->jsonOrRedirect(false, 'Format jadwal tidak valid. Gunakan format YYYY-MM-DD HH:MM.', 422);
                    }
                    $data['jadwal_interview_' . $step] = $parsed;
                }
                if ($linkZoom !== '') {
                    $data['link_zoom_' . $step] = $linkZoom;
                }
            }
        } elseif ($catatan !== '') {
            $data['catatan_admin'] = $catatan;
        }

        if (!$this->pendaftaranModel->update($id, $data)) {
            return $this->jsonOrRedirect(false, 'Gagal menyimpan perubahan: ' . implode(', ', $this->pendaftaranModel->errors()), 500);
        }

        $updated = $this->pendaftaranModel->find($id);

        $payload = array_merge([
            'success'      => true,
            'message'      => 'Status berhasil diperbarui menjadi: ' . str_replace('_', ' ', $updated['status']),
            'id'           => (int) $id,
            'has_email'    => !empty($updated['email']),
            'status'       => $updated['status'],
            'status_label' => str_replace('_', ' ', $updated['status']),
            'badge_html'   => $this->renderStatusBadge($updated),
            'aksi_html'    => $this->renderAksiCell($updated),
        ], $this->itemPayload($updated));

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($payload);
        }

        return redirect()->back()->with('success', $payload['message']);
    }

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

    // ==============================================================
    //  HELPER PRIVATE
    // ==============================================================

    /**
     * FIX: sebelumnya kode ini pakai nama field ('surat_pengantar') sebagai
     * nama folder juga, padahal Pendaftaran::store() menyimpan filenya di
     * folder 'surat' (bukan 'surat_pengantar'). Akibatnya file surat
     * pengantar tidak pernah kehapus fisik walau datanya sudah dihapus dari
     * database (jadi sampah file menumpuk terus).
     */
    private function deleteCandidateFiles(array $pendaftaran): void
    {
        $map = [
            'cv'              => 'cv',
            'surat_pengantar' => 'surat',
            'ktm'             => 'ktm',
        ];

        foreach ($map as $field => $folder) {
            if (!empty($pendaftaran[$field])) {
                $path = WRITEPATH . 'uploads/' . $folder . '/' . $pendaftaran[$field];
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }
    }

    /**
     * Kebijakan retensi data, 2 tahap (dijalankan otomatis tiap kali dashboard
     * dibuka, atau lewat cron `php spark cleanup:pendaftaran`):
     *
     *  TAHAP 1 - masuk ARSIP dulu (belum dihapus, cuma disembunyikan dari
     *  tabel utama & bisa dipulihkan admin) begitu kena tenggat:
     *    - Ditolak / Tidak Lolos Interview  -> arsip 7 hari setelah status ditetapkan
     *    - Menunggu                        -> arsip 6 bulan setelah tanggal daftar
     *    - Lolos_Final / Diterima          -> arsip 1 tahun setelah status ditetapkan
     *
     *  TAHAP 2 - HAPUS PERMANEN kalau sudah 7 hari di arsip dan tidak
     *  dipulihkan admin.
     */
    private function runRetentionCleanup(): void
    {
        // ---- TAHAP 1: masuk arsip ----
        $rejected = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->whereIn('status', ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'])
            ->where('status_changed_at IS NOT NULL')
            ->where('status_changed_at <=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->findAll();
        $this->archiveCandidates($rejected, 'Ditolak (7 hari)');

        $waiting = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->where('status', 'Menunggu')
            ->where('created_at <=', date('Y-m-d H:i:s', strtotime('-6 months')))
            ->findAll();
        $this->archiveCandidates($waiting, 'Menunggu (6 bulan)');

        $accepted = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->whereIn('status', ['Lolos_Final', 'Diterima'])
            ->where('status_changed_at IS NOT NULL')
            ->where('status_changed_at <=', date('Y-m-d H:i:s', strtotime('-1 year')))
            ->findAll();
        $this->archiveCandidates($accepted, 'Lolos/Diterima (1 tahun)');

        // ---- TAHAP 2: sudah di arsip 7 hari -> hapus permanen ----
        $expired = $this->pendaftaranModel
            ->where('is_archived', 1)
            ->where('archived_at IS NOT NULL')
            ->where('archived_at <=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->findAll();
        $this->purgeCandidates($expired, 'sudah 7 hari di arsip');
    }

    private function archiveCandidates(array $rows, string $reason): void
    {
        foreach ($rows as $row) {
            $this->pendaftaranModel->update($row['id'], [
                'is_archived'     => 1,
                'archived_at'     => date('Y-m-d H:i:s'),
                'archived_reason' => $reason,
            ]);
            log_message('info', "Auto-arsip kandidat #{$row['id']} ({$row['nama_lengkap']}) - {$reason}. Akan dihapus permanen 7 hari lagi kalau tidak dipulihkan.");
        }
    }

    private function purgeCandidates(array $rows, string $reason): void
    {
        foreach ($rows as $row) {
            $this->deleteCandidateFiles($row);
            $this->pendaftaranModel->delete($row['id']);
            log_message('info', "Hapus permanen kandidat #{$row['id']} ({$row['nama_lengkap']}) - {$reason}");
        }
    }


    private function getInterviewStep($status)
    {
        if (preg_match('/Interview_(\d)/', $status, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }

    private function parseJadwal(string $jadwal): ?string
    {
        $jadwal = str_replace('T', ' ', $jadwal);
        $timestamp = strtotime($jadwal);
        if ($timestamp === false) {
            return null;
        }
        return date('Y-m-d H:i:s', $timestamp);
    }

    private function jsonOrRedirect(bool $success, string $message, int $code = 200)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode($code)->setJSON(['success' => $success, 'message' => $message]);
        }
        return $success
            ? redirect()->back()->with('success', $message)
            : redirect()->back()->with('error', $message);
    }

    private function ajaxOr($id, bool $success, string $message, int $code, callable $fallback)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode($code)->setJSON(['success' => $success, 'message' => $message]);
        }
        return $fallback();
    }

    /**
     * Data lengkap 1 kandidat + label yang sudah rapi, dipakai untuk mengisi
     * modal aksi di dashboard (dropdown status, jadwal, link zoom, catatan).
     */
    private function itemPayload(array $item): array
    {
        $out = ['item' => []];
        $fields = [
            'id', 'nama_lengkap', 'email', 'nomor_whatsapp', 'status',
            'jadwal_interview_1', 'jadwal_interview_2', 'jadwal_interview_3',
            'link_zoom_1', 'link_zoom_2', 'link_zoom_3',
            'catatan_interview_1', 'catatan_interview_2', 'catatan_interview_3',
            'catatan_admin', 'email_terkirim',
            'is_archived', 'archived_at', 'archived_reason',
        ];
        foreach ($fields as $f) {
            $out['item'][$f] = $item[$f] ?? null;
        }
        return $out;
    }

    private function statusBadgeClass(string $status): string
    {
        return match (true) {
            in_array($status, ['Diterima', 'Lolos_Final'], true) => 'bg-success',
            in_array($status, ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'], true) => 'bg-danger',
            $status === 'Lolos_Interview_1' => 'bg-primary',   // biru
            $status === 'Lolos_Interview_2' => 'bg-info',      // cyan
            $status === 'Lolos_Interview_3' => 'bg-purple',    // ungu (custom, lihat CSS di dashboard.php)
            default => 'bg-warning',                            // Menunggu
        };
    }

    private function renderStatusBadge(array $item): string
    {
        $status = $item['status'];
        $class = $this->statusBadgeClass($status);

        $html = '<span class="badge ' . $class . '">' . esc(str_replace('_', ' ', $status)) . '</span>';

        $step = $this->getInterviewStep($status);
        if ($step > 0 && !empty($item['jadwal_interview_' . $step])) {
            $html .= '<div class="small text-muted mt-1"><i class="bi bi-calendar-event"></i> '
                . date('d/m/Y H:i', strtotime($item['jadwal_interview_' . $step])) . ' WIB</div>';
        }

        return $html;
    }

    /**
     * Aksi disederhanakan: cuma ikon Aksi (buka modal), WA, Hapus.
     * Tidak ada lagi badge status duplikat / tombol centang-silang terpisah.
     */
    private function renderAksiCell(array $item): string
    {
        $id = (int) $item['id'];
        $nama = esc($item['nama_lengkap'], 'js');

        $html = '<div class="d-flex gap-1 justify-content-center flex-nowrap">';
        $html .= '<button type="button" class="btn btn-outline-primary btn-sm" onclick="openActionModal(' . $id . ", '" . $nama . "')\" title=\"Lihat Detail & Kelola Status\"><i class=\"bi bi-eye\"></i></button>";
        $html .= '<button type="button" class="btn btn-outline-success btn-sm" onclick="openWaLink(' . $id . ')" title="Ingatkan via WhatsApp"><i class="bi bi-whatsapp"></i></button>';
        $html .= '<button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusData(' . $id . ')" title="Hapus"><i class="bi bi-trash"></i></button>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return array{sent: bool, error: string}
     */
    private function sendStatusEmail(array $item): array
    {
        try {
            $email = Services::email($this->emailConfig());
            $tpl = $this->buildEmailTemplate($item);

            $email->setFrom('farezaairo@gmail.com', 'Future Talent Program');
            $email->setTo($item['email']);
            $email->setSubject($tpl['subject']);
            $email->setMessage($tpl['body']);

            $sent = $email->send();

            if (!$sent) {
                $debug = $email->printDebugger(['headers']);
                log_message('error', 'Gagal kirim email ke {email}: {debug}', ['email' => $item['email'], 'debug' => $debug]);
                return ['sent' => false, 'error' => 'Gagal kirim, cek log CI4 (writable/logs) untuk detail SMTP-nya.'];
            }

            return ['sent' => true, 'error' => ''];
        } catch (\Throwable $e) {
            log_message('error', 'Exception saat kirim email: ' . $e->getMessage());
            return ['sent' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Konfigurasi SMTP ini SENGAJA disamakan persis dengan yang dipakai di
     * Pendaftaran::sendEmailToken() (yang sudah terbukti berhasil kirim email
     * token pendaftaran). Kalau email token pendaftaran jalan, notifikasi
     * status di sini juga akan jalan karena pakai kredensial & pengaturan
     * yang sama.
     *
     * CATATAN KEAMANAN: idealnya App Password Gmail ini disimpan di .env,
     * bukan hardcode di kode. Tapi supaya konsisten & pasti jalan seperti
     * punya Anda, saya samakan dulu langsung di sini.
     */
    private function emailConfig(): \Config\Email
    {
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

        return $config;
    }

    private function buildEmailTemplate(array $item): array
    {
        $nama = esc($item['nama_lengkap']);
        $status = $item['status'];
        $step = $this->getInterviewStep($status);
        $token = esc($item['token_pendaftaran'] ?? '-');

        if (str_starts_with($status, 'Lolos_Interview_')) {
            $jadwal = $item['jadwal_interview_' . $step] ?? null;
            $zoom = $item['link_zoom_' . $step] ?? null;
            $jadwalText = $jadwal ? date('l, d F Y \p\u\k\u\l H:i', strtotime($jadwal)) . ' WIB' : 'akan diinformasikan kemudian';

            $subject = "Undangan Interview Tahap {$step} - Future Talent Program";
            $headline = "Undangan Interview Tahap {$step}";
            $intro = "Selamat! Anda dijadwalkan untuk mengikuti <strong>Interview Tahap {$step}</strong> pada program Future Talent Program.";
            $boxLabel = "Jadwal Interview";
            $boxValue = esc($jadwalText);
            $extra = $zoom
                ? "<div style='text-align:center;margin-top:20px;'><a href='" . esc($zoom) . "' style='background-color:#1e3a8a;color:#ffffff;padding:12px 30px;text-decoration:none;font-size:15px;font-weight:bold;border-radius:5px;display:inline-block;'>Gabung Link Zoom / Meet</a></div>"
                : "<p style='font-size:14px;color:#666;'>Link Zoom akan diinformasikan lebih lanjut oleh tim kami.</p>";
            $footerNote = "Mohon hadir 10 menit sebelum jadwal dan pastikan koneksi internet Anda stabil.";
        } elseif (in_array($status, ['Diterima', 'Lolos_Final'], true)) {
            $subject = "Selamat! Anda Diterima - Future Talent Program";
            $headline = "Selamat, Anda Diterima! 🎉";
            $intro = "Selamat! Anda dinyatakan <strong>LOLOS</strong> dan diterima pada program <strong>Future Talent Program</strong>.";
            $boxLabel = "Nomor Token Anda";
            $boxValue = $token;
            $extra = "<p style='font-size:14px;color:#666;'>Tim kami akan segera menghubungi Anda untuk informasi langkah selanjutnya.</p>";
            $footerNote = "Terima kasih atas partisipasi Anda dalam seluruh rangkaian seleksi.";
        } elseif (in_array($status, ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'], true)) {
            $subject = "Informasi Status Pendaftaran - Future Talent Program";
            $headline = "Informasi Status Pendaftaran";
            $intro = "Terima kasih atas partisipasi Anda pada proses seleksi <strong>Future Talent Program</strong>.";
            $boxLabel = "Status";
            $boxValue = "Belum dapat melanjutkan ke tahap berikutnya";
            $extra = "<p style='font-size:14px;color:#666;'>Semoga sukses untuk kesempatan berikutnya. Jangan berkecil hati untuk mencoba kembali di kesempatan lain.</p>";
            $footerNote = "Terima kasih telah meluangkan waktu mengikuti proses seleksi kami.";
        } else {
            $subject = "Informasi Pendaftaran - Future Talent Program";
            $headline = "Pendaftaran Anda Sedang Diproses";
            $intro = "Terima kasih telah mendaftar pada program <strong>Future Talent Program</strong>.";
            $boxLabel = "Nomor Token Anda";
            $boxValue = $token;
            $extra = "<p style='font-size:14px;color:#666;'>Pendaftaran Anda sedang kami proses. Mohon ditunggu informasi selanjutnya.</p>";
            $footerNote = "Simpan token Anda untuk keperluan pelacakan status pendaftaran.";
        }

        $logoUrl = 'https://cdn-icons-png.flaticon.com/512/3135/3135665.png';

        $body = "
        <div style='background-color: #f4f6f9; padding: 30px 15px; font-family: Arial, sans-serif; color: #333;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;'>
                <tr>
                    <td align='center' style='background-color: #1e3a8a; padding: 30px 20px;'>
                        <img src='{$logoUrl}' alt='Logo FTP' style='width: 80px; height: auto; margin-bottom: 10px; display: block;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;'>Future Talent Program</h2>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 40px 30px;'>
                        <h3 style='margin-top:0;color:#1e3a8a;'>{$headline}</h3>
                        <p style='font-size: 16px; line-height: 1.6; margin-top: 0;'>Halo <strong>{$nama}</strong>,</p>
                        <p style='font-size: 15px; line-height: 1.6; color: #555;'>{$intro}</p>

                        <div style='background-color: #f0f4f8; border-left: 4px solid #1e3a8a; border-radius: 4px; padding: 20px; margin: 30px 0; text-align: center;'>
                            <span style='font-size: 13px; text-transform: uppercase; color: #666; display: block; margin-bottom: 5px;'>{$boxLabel}</span>
                            <span style='font-size: 20px; font-weight: bold; color: #1e3a8a; font-family: monospace;'>{$boxValue}</span>
                        </div>

                        {$extra}

                        <p style='font-size: 13px; line-height: 1.6; color: #888; margin-top: 25px;'>{$footerNote}</p>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #edf2f7; font-size: 12px; color: #999;'>
                        <p style='margin: 0 0 5px 0;'>Email ini dikirim otomatis oleh sistem rekrutmen FTP.</p>
                        <p style='margin: 0;'>&copy; " . date('Y') . " Future Talent Program. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </div>
        ";

        return ['subject' => $subject, 'body' => $body];
    }

    private function buildWaTemplate(array $item): array
    {
        $nama = $item['nama_lengkap'];
        $status = $item['status'];
        $step = $this->getInterviewStep($status);

        if (str_starts_with($status, 'Lolos_Interview_')) {
            $jadwal = $item['jadwal_interview_' . $step] ?? null;
            $zoom = $item['link_zoom_' . $step] ?? null;
            $jadwalText = $jadwal ? date('l, d F Y', strtotime($jadwal)) : '(menyusul)';
            $jamText = $jadwal ? date('H:i', strtotime($jadwal)) . ' WIB' : '(menyusul)';

            $message = "Halo *{$nama}*,\n\n"
                . "Selamat! Anda dijadwalkan mengikuti *Interview Tahap {$step}* program magang IOH Semarang.\n\n"
                . "🗓️ Hari/Tanggal: {$jadwalText}\n"
                . "⏰ Waktu: {$jamText}\n"
                . "💻 Link Zoom: " . ($zoom ?: '-') . "\n\n"
                . "Mohon hadir 10 menit sebelum jadwal dan pastikan koneksi internet stabil ya. Sampai jumpa!\n\n"
                . "Salam,\nTim Rekrutmen Magang IOH Semarang";
        } elseif (in_array($status, ['Diterima', 'Lolos_Final'], true)) {
            $message = "Halo *{$nama}*,\n\n"
                . "Selamat! Anda dinyatakan *LOLOS* dan diterima pada program magang IOH Semarang. 🎉\n"
                . "Tim kami akan segera menghubungi Anda untuk info langkah selanjutnya.\n\n"
                . "Salam,\nTim Rekrutmen Magang IOH Semarang";
        } elseif (in_array($status, ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'], true)) {
            $message = "Halo *{$nama}*,\n\n"
                . "Terima kasih atas partisipasi Anda pada seleksi magang IOH Semarang.\n"
                . "Untuk saat ini kami belum dapat melanjutkan proses Anda ke tahap berikutnya. Semoga sukses di kesempatan berikutnya!\n\n"
                . "Salam,\nTim Rekrutmen Magang IOH Semarang";
        } else {
            $message = "Halo *{$nama}*,\n\n"
                . "Terima kasih telah mendaftar program magang IOH Semarang. Pendaftaran Anda sedang kami proses, mohon ditunggu ya.\n\n"
                . "Salam,\nTim Rekrutmen Magang IOH Semarang";
        }

        $number = $this->normalizeWaNumber($item['nomor_whatsapp'] ?? '');
        $url = $number ? 'https://wa.me/' . $number . '?text=' . rawurlencode($message) : null;

        return ['message' => $message, 'url' => $url];
    }

    private function normalizeWaNumber(string $number): ?string
    {
        $digits = preg_replace('/\D/', '', $number);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }
        return $digits;
    }
}