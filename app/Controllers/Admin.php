<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\PendaftaranModel;
use App\Models\AppSettingsModel;
use App\Services\InterviewNotificationService;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

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

        // Otomatis Complete jika periode_selesai sudah lewat (Diterima -> Complete)
        try {
            $this->runAutoComplete();
        } catch (\Throwable $e) {
            log_message('error', 'AutoComplete error: ' . $e->getMessage());
        }

        // Hapus otomatis data yang sudah lewat tenggat waktu retensi
        try {
            $this->runRetentionCleanup();
        } catch (\Throwable $e) {
            log_message('error', 'Retention cleanup error: ' . $e->getMessage());
        }

        $isArsip = $this->request->getGet('arsip') == '1';

        $data['total_pendaftar'] = $this->pendaftaranModel->where('is_archived', 0)->countAll();

        $data['total_diterima'] = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->whereIn('status', ['Diterima', 'Complete'])
            ->countAllResults();

        $data['total_ditolak'] = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->where('status', 'Ditolak')
            ->countAllResults();

        $data['total_menunggu'] = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->whereIn('status', ['Menunggu', 'Progress'])
            ->countAllResults();

        $data['total_arsip'] = $this->pendaftaranModel->where('is_archived', 1)->countAllResults();

        $keyword = $this->request->getGet('keyword');
        $divisiFilter = $this->request->getGet('divisi');
        $jenisFilter = $this->request->getGet('jenis');
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
                ->orLike('divisi_pilihan', $keyword)
            ->groupEnd();
        }

        if (!empty($divisiFilter)) {
            $modelQuery = $modelQuery->where('divisi_pilihan', $divisiFilter);
        }

        if (!empty($jenisFilter)) {
            $modelQuery = $modelQuery->where('jenis_magang', $jenisFilter);
        }

        $sortField = $isArsip ? 'archived_at' : 'created_at';
        $data['pendaftaran'] = $modelQuery->orderBy($sortField, 'DESC')->paginate(15, 'pendaftaran');
        $data['pager'] = $this->pendaftaranModel->pager;
        $data['keyword'] = $keyword;
        $data['divisi_filter'] = $divisiFilter;
        $data['jenis_filter'] = $jenisFilter;
        $data['is_arsip'] = $isArsip;
        $data['registration_open'] = $this->settingsModel->getValue('registration_open') ?? '1';
        $data['kota_pilihan_options'] = (new \Config\InternshipLocations())->kotaPilihan;
        $data['kota_magang_options']  = $data['kota_pilihan_options'];

        if ($this->request->isAJAX()) {
            return view('admin/_table_data', $data);
        }

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

        // Lazy auto-complete per-record: pastikan detail menampilkan status terbaru
        try {
            $this->pendaftaranModel->autoCompleteExpired();
        } catch (\Throwable $e) {
            log_message('error', 'AutoComplete detail error: ' . $e->getMessage());
        }

        $data['item'] = $this->pendaftaranModel->find($id);

        if (!$data['item']) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // Jika record ini sendiri sudah lewat periode tapi bulk tadi belum commit (race), cek satu per satu
        if (PendaftaranModel::shouldBeComplete($data['item'])) {
            $this->pendaftaranModel->update($id, ['status' => 'Complete', 'status_changed_at' => date('Y-m-d H:i:s')]);
            $data['item'] = $this->pendaftaranModel->find($id);
        }

        return view('admin/detail', $data);
    }

    public function exportExcel()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            // Pastikan data yang diekspor sudah auto-Complete jika periode habis
            try {
                $this->pendaftaranModel->autoCompleteExpired();
            } catch (\Throwable $e) {
                log_message('error', 'AutoComplete exportExcel error: ' . $e->getMessage());
            }

            $isArsip = $this->request->getGet('arsip') == '1';
            $keyword = $this->request->getGet('keyword');
            $mode    = $this->request->getGet('mode') ?? 'custom'; // 'custom' or 'all'

            $builder = $this->pendaftaranModel
                ->where('is_archived', $isArsip ? 1 : 0)
                ->whereIn('status', ['Diterima', 'Complete']);

            if (!empty($keyword)) {
                $builder->groupStart()
                    ->like('nama_lengkap', $keyword)
                    ->orLike('email', $keyword)
                    ->orLike('asal_kampus', $keyword)
                    ->orLike('program_studi', $keyword)
                    ->orLike('nomor_whatsapp', $keyword)
                    ->orLike('token_pendaftaran', $keyword)
                    ->orLike('status', $keyword)
                    ->orLike('divisi_pilihan', $keyword);

                // Cek kolom opsional agar tidak memicu error SQL jika kolom belum ada di DB production
                try {
                    $dbFields = $this->pendaftaranModel->db->getFieldNames('pendaftaran_magang');
                    if (in_array('regional_interview', $dbFields, true)) {
                        $builder->orLike('regional_interview', $keyword);
                    }
                    if (in_array('kota_pilihan', $dbFields, true)) {
                        $builder->orLike('kota_pilihan', $keyword);
                    } elseif (in_array('kota_magang', $dbFields, true)) {
                        $builder->orLike('kota_magang', $keyword);
                    }
                } catch (\Throwable $ignored) {
                    // Abaikan jika tidak bisa membaca field schema
                }

                $builder->groupEnd();
            }

            $sortField = $isArsip ? 'archived_at' : 'created_at';
            $pendaftaran = $builder->orderBy($sortField, 'DESC')->findAll();

            // Cek apakah PhpSpreadsheet dan ekstensi ZipArchive tersedia (wajib untuk format .xlsx).
            // Jika salah satunya tidak tersedia di server hosting, fallback otomatis ke format CSV agar unduhan tetap berhasil.
            if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet') || !class_exists('ZipArchive')) {
                log_message('warning', "PhpSpreadsheet atau ZipArchive tidak tersedia di server hosting. Menggunakan fallback format CSV untuk export.");
                return $this->exportCsvFallback($pendaftaran, $mode, $isArsip);
            }

            // Pastikan temporary file PhpSpreadsheet tidak error di hosting dengan batasan open_basedir
            if (is_dir(WRITEPATH . 'cache')) {
                \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(false);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($isArsip ? 'Data Arsip Diterima' : 'Data Peserta Diterima');

            // Tampilkan garis kisi (gridlines)
            $sheet->setShowGridLines(true);

            $rowIndex = 2;
            $no = 1;

        if ($mode === 'all' || $mode === 'dashboard' || $mode === 'original') {
            // Mode SESUAI TAMPILAN DASHBOARD (Urutan Asli Dashboard)
            $headers = [
                'No',
                'Token Pendaftaran',
                'Nama Lengkap',
                'Email',
                'WhatsApp',
                'Nomor Darurat',
                'Asal Kampus',
                'Program Studi',
                'Semester',
                'Regional Interview',
                'Kota Pilihan',
                'Divisi Pilihan',
                'Jenis Magang',
                'Status',
                'Periode Mulai',
                'Periode Selesai',
                'Tanggal Daftar',
                'Terakhir Diubah',
                'Catatan Admin / Arsip'
            ];

            // Tulis header di Baris 1
            $colIndex = 1;
            foreach ($headers as $headerText) {
                $sheet->setCellValue([$colIndex, 1], $headerText);
                $colIndex++;
            }

            foreach ($pendaftaran as $row) {
                $periodeMulai   = !empty($row['periode_mulai']) ? date('d/m/Y', strtotime($row['periode_mulai'])) : '-';
                $periodeSelesai = !empty($row['periode_selesai']) ? date('d/m/Y', strtotime($row['periode_selesai'])) : '-';
                $tglDaftar      = !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-';
                $tglUbah        = !empty($row['updated_at']) ? date('d/m/Y H:i', strtotime($row['updated_at'])) : '-';
                $catatan        = $row['catatan_admin'] ?? $row['archived_reason'] ?? $row['catatan'] ?? '-';

                $sheet->setCellValue([1, $rowIndex], $no++);
                $sheet->setCellValueExplicit([2, $rowIndex], (string) ($row['token_pendaftaran'] ?? '-'), DataType::TYPE_STRING);
                $sheet->setCellValue([3, $rowIndex], $row['nama_lengkap'] ?? '-');
                $sheet->setCellValue([4, $rowIndex], $row['email'] ?? '-');
                $sheet->setCellValueExplicit([5, $rowIndex], (string) ($row['nomor_whatsapp'] ?? '-'), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit([6, $rowIndex], (string) ($row['nomor_darurat'] ?? '-'), DataType::TYPE_STRING);
                $sheet->setCellValue([7, $rowIndex], $row['asal_kampus'] ?? '-');
                $sheet->setCellValue([8, $rowIndex], $row['program_studi'] ?? '-');
                $sheet->setCellValue([9, $rowIndex], $row['semester'] ?? '-');
                $sheet->setCellValue([10, $rowIndex], $row['regional_interview'] ?? '-');
                $sheet->setCellValue([11, $rowIndex], $row['kota_pilihan'] ?? '-');
                $sheet->setCellValue([12, $rowIndex], $row['divisi_pilihan'] ?? '-');
                $sheet->setCellValue([13, $rowIndex], $row['jenis_magang'] ?? '-');
                $sheet->setCellValue([14, $rowIndex], $row['status'] ?? '-');
                $sheet->setCellValue([15, $rowIndex], $periodeMulai);
                $sheet->setCellValue([16, $rowIndex], $periodeSelesai);
                $sheet->setCellValue([17, $rowIndex], $tglDaftar);
                $sheet->setCellValue([18, $rowIndex], $tglUbah);
                $sheet->setCellValue([19, $rowIndex], $catatan);

                $sheet->getRowDimension($rowIndex)->setRowHeight(22);
                $rowIndex++;
            }
        } else {
            // Mode CUSTOM (12 Kolom: Kolom Utama + Status Magang + Suket & Sertif)
            $headers = [
                'No',
                'Token Pendaftaran',
                'Nama Lengkap',
                'Divisi Pilihan',
                'Asal Kampus',
                'Program Studi',
                'Periode Mulai',
                'Periode Selesai',
                'Status Magang',
                'Suket Penerimaan',
                'Suket Selesai',
                'Sertif Selesai'
            ];

            // Tulis header di Baris 1
            $colIndex = 1;
            foreach ($headers as $headerText) {
                $sheet->setCellValue([$colIndex, 1], $headerText);
                $colIndex++;
            }

            foreach ($pendaftaran as $row) {
                $periodeMulai   = !empty($row['periode_mulai']) ? date('d/m/Y', strtotime($row['periode_mulai'])) : '-';
                $periodeSelesai = !empty($row['periode_selesai']) ? date('d/m/Y', strtotime($row['periode_selesai'])) : '-';

                $currentStatus = $row['status'] ?? '-';
                if ($currentStatus === 'Diterima') {
                    $statusDisplay = 'Active';
                } elseif ($currentStatus === 'Complete') {
                    $statusDisplay = 'Completed';
                } else {
                    $statusDisplay = $currentStatus;
                }

                $sheet->setCellValue([1, $rowIndex], $no++);
                $sheet->setCellValueExplicit([2, $rowIndex], (string) ($row['token_pendaftaran'] ?? '-'), DataType::TYPE_STRING);
                $sheet->setCellValue([3, $rowIndex], $row['nama_lengkap'] ?? '-');
                $sheet->setCellValue([4, $rowIndex], $row['divisi_pilihan'] ?? '-');
                $sheet->setCellValue([5, $rowIndex], $row['asal_kampus'] ?? '-');
                $sheet->setCellValue([6, $rowIndex], $row['program_studi'] ?? '-');
                $sheet->setCellValue([7, $rowIndex], $periodeMulai);
                $sheet->setCellValue([8, $rowIndex], $periodeSelesai);
                $sheet->setCellValue([9, $rowIndex], $statusDisplay);
                $sheet->setCellValue([10, $rowIndex], 'Belum Diproses');
                $sheet->setCellValue([11, $rowIndex], 'Belum Diproses');
                $sheet->setCellValue([12, $rowIndex], 'Belum Diproses');

                $sheet->getRowDimension($rowIndex)->setRowHeight(22);
                $rowIndex++;
            }

            // Pasang Excel Data Validation Dropdown pada kolom Status Magang (Kolom I) dan Suket/Sertif (Kolom J, K, L)
            $maxValidationRow = max(100, $rowIndex + 50);
            for ($r = 2; $r <= $maxValidationRow; $r++) {
                // Status Magang (Kolom I)
                $valStatus = $sheet->getCell('I' . $r)->getDataValidation();
                $valStatus->setType(DataValidation::TYPE_LIST);
                $valStatus->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $valStatus->setAllowBlank(true);
                $valStatus->setShowInputMessage(true);
                $valStatus->setShowErrorMessage(true);
                $valStatus->setShowDropDown(true);
                $valStatus->setPromptTitle('Status Magang');
                $valStatus->setPrompt('Pilih Active atau Completed');
                $valStatus->setFormula1('"Active,Completed"');

                // Suket Penerimaan (Kolom J)
                $valSuketP = $sheet->getCell('J' . $r)->getDataValidation();
                $valSuketP->setType(DataValidation::TYPE_LIST);
                $valSuketP->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $valSuketP->setAllowBlank(true);
                $valSuketP->setShowDropDown(true);
                $valSuketP->setFormula1('"Belum Diproses,Sedang Diproses,Sudah Dikirim"');

                // Suket Selesai (Kolom K)
                $valSuketS = $sheet->getCell('K' . $r)->getDataValidation();
                $valSuketS->setType(DataValidation::TYPE_LIST);
                $valSuketS->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $valSuketS->setAllowBlank(true);
                $valSuketS->setShowDropDown(true);
                $valSuketS->setFormula1('"Belum Diproses,Sedang Diproses,Sudah Dikirim"');

                // Sertif Selesai (Kolom L)
                $valSertif = $sheet->getCell('L' . $r)->getDataValidation();
                $valSertif->setType(DataValidation::TYPE_LIST);
                $valSertif->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $valSertif->setAllowBlank(true);
                $valSertif->setShowDropDown(true);
                $valSertif->setFormula1('"Belum Diproses,Sedang Diproses,Sudah Dikirim"');
            }

            // === CONDITIONAL FORMATTING (PEWARNAAN OTOMATIS) ===
            // 1. Rule untuk Status Magang (Kolom I): Active (Hijau) & Completed (Biru)
            $condActive = new Conditional();
            $condActive->setConditionType(Conditional::CONDITION_CELLIS);
            $condActive->setOperatorType(Conditional::OPERATOR_EQUAL);
            $condActive->addCondition('"Active"');
            $condActive->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');
            $condActive->getStyle()->getFont()->getColor()->setRGB('155724');
            $condActive->getStyle()->getFont()->setBold(true);

            $condCompleted = new Conditional();
            $condCompleted->setConditionType(Conditional::CONDITION_CELLIS);
            $condCompleted->setOperatorType(Conditional::OPERATOR_EQUAL);
            $condCompleted->addCondition('"Completed"');
            $condCompleted->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCE5FF');
            $condCompleted->getStyle()->getFont()->getColor()->setRGB('004085');
            $condCompleted->getStyle()->getFont()->setBold(true);

            $sheet->getStyle('I2:I' . $maxValidationRow)->setConditionalStyles([$condActive, $condCompleted]);

            // 2. Rule untuk Suket & Sertif (Kolom J, K, L): Belum Diproses (Merah Muda), Sedang Diproses (Kuning), Sudah Dikirim (Hijau Muda)
            $condBelum = new Conditional();
            $condBelum->setConditionType(Conditional::CONDITION_CELLIS);
            $condBelum->setOperatorType(Conditional::OPERATOR_EQUAL);
            $condBelum->addCondition('"Belum Diproses"');
            $condBelum->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');
            $condBelum->getStyle()->getFont()->getColor()->setRGB('721C24');
            $condBelum->getStyle()->getFont()->setBold(true);

            $condSedang = new Conditional();
            $condSedang->setConditionType(Conditional::CONDITION_CELLIS);
            $condSedang->setOperatorType(Conditional::OPERATOR_EQUAL);
            $condSedang->addCondition('"Sedang Diproses"');
            $condSedang->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF3CD');
            $condSedang->getStyle()->getFont()->getColor()->setRGB('856404');
            $condSedang->getStyle()->getFont()->setBold(true);

            $condSudah = new Conditional();
            $condSudah->setConditionType(Conditional::CONDITION_CELLIS);
            $condSudah->setOperatorType(Conditional::OPERATOR_EQUAL);
            $condSudah->addCondition('"Sudah Dikirim"');
            $condSudah->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');
            $condSudah->getStyle()->getFont()->getColor()->setRGB('155724');
            $condSudah->getStyle()->getFont()->setBold(true);

            $docRules = [$condBelum, $condSedang, $condSudah];
            $sheet->getStyle('J2:J' . $maxValidationRow)->setConditionalStyles($docRules);
            $sheet->getStyle('K2:K' . $maxValidationRow)->setConditionalStyles($docRules);
            $sheet->getStyle('L2:L' . $maxValidationRow)->setConditionalStyles($docRules);
        }

        $highestColumn = $sheet->getHighestColumn();

        // Styling baris header (Indosat Red Accent)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C00000'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '8B0000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $lastRow = max(2, $rowIndex - 1);

        // Styling data cells
        if ($rowIndex > 2) {
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E0E0E0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A2:' . $highestColumn . $lastRow)->applyFromArray($dataStyle);

            if ($mode === 'all' || $mode === 'dashboard' || $mode === 'original') {
                $centerCols = ['A', 'B', 'I', 'J', 'M', 'N', 'O', 'P', 'Q', 'R'];
            } else {
                $centerCols = ['A', 'B', 'G', 'H', 'I', 'J', 'K', 'L'];
            }

            foreach ($centerCols as $col) {
                $sheet->getStyle($col . '2:' . $col . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // Aktifkan Filter dropdown pada baris header
        $sheet->setAutoFilter('A1:' . $highestColumn . $lastRow);

        // Atur lebar kolom yang rapi & proporsional (aman di server Linux tanpa bergantung font FreeType GD)
        if ($mode === 'all' || $mode === 'dashboard' || $mode === 'original') {
            $columnWidths = [
                'A' => 6,  'B' => 16, 'C' => 28, 'D' => 28, 'E' => 18, 'F' => 18,
                'G' => 28, 'H' => 24, 'I' => 10, 'J' => 20, 'K' => 20, 'L' => 24,
                'M' => 14, 'N' => 14, 'O' => 15, 'P' => 15, 'Q' => 18, 'R' => 18, 'S' => 30
            ];
        } else {
            $columnWidths = [
                'A' => 6,  'B' => 16, 'C' => 28, 'D' => 24, 'E' => 28, 'F' => 24,
                'G' => 15, 'H' => 15, 'I' => 18, 'J' => 20, 'K' => 20, 'L' => 20
            ];
        }
        foreach ($columnWidths as $colLetter => $width) {
            $sheet->getColumnDimension($colLetter)->setWidth($width);
        }

        $modeSuffix = ($mode === 'all' || $mode === 'dashboard' || $mode === 'original') ? '_dashboard_' : '_kustom_';
        $filename = ($isArsip ? 'data_arsip_diterima' : 'data_peserta_diterima') . $modeSuffix . date('Y-m-d_His') . '.xlsx';

        // Bersihkan output buffer agar file zip/xlsx tidak korup
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
        } catch (\Throwable $e) {
            log_message('error', 'Export Excel Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->to(site_url('admin/dashboard'))->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Fallback otomatis ke format CSV jika server hosting belum mengaktifkan ekstensi ZipArchive.
     */
    private function exportCsvFallback(array $pendaftaran, string $mode, bool $isArsip)
    {
        $modeSuffix = ($mode === 'all' || $mode === 'dashboard' || $mode === 'original') ? '_dashboard_' : '_kustom_';
        $filename   = ($isArsip ? 'data_arsip_diterima' : 'data_peserta_diterima') . $modeSuffix . date('Y-m-d_His') . '.csv';

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM agar Excel di Windows/Mac menampilkan teks & nomor dengan rapi
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if ($mode === 'all' || $mode === 'dashboard' || $mode === 'original') {
            fputcsv($output, [
                'No', 'Token Pendaftaran', 'Nama Lengkap', 'Email', 'WhatsApp', 'Nomor Darurat',
                'Asal Kampus', 'Program Studi', 'Semester', 'Regional Interview', 'Kota Pilihan',
                'Divisi Pilihan', 'Jenis Magang', 'Status', 'Periode Mulai', 'Periode Selesai',
                'Tanggal Daftar', 'Terakhir Diubah', 'Catatan Admin / Arsip'
            ], ',', '"', "\\");

            $no = 1;
            foreach ($pendaftaran as $row) {
                $periodeMulai   = !empty($row['periode_mulai']) ? date('d/m/Y', strtotime($row['periode_mulai'])) : '-';
                $periodeSelesai = !empty($row['periode_selesai']) ? date('d/m/Y', strtotime($row['periode_selesai'])) : '-';
                $tglDaftar      = !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-';
                $tglUbah        = !empty($row['updated_at']) ? date('d/m/Y H:i', strtotime($row['updated_at'])) : '-';
                $catatan        = $row['catatan_admin'] ?? $row['archived_reason'] ?? $row['catatan'] ?? '-';

                fputcsv($output, [
                    $no++,
                    (string) ($row['token_pendaftaran'] ?? '-'),
                    $row['nama_lengkap'] ?? '-',
                    $row['email'] ?? '-',
                    (string) ($row['nomor_whatsapp'] ?? '-'),
                    (string) ($row['nomor_darurat'] ?? '-'),
                    $row['asal_kampus'] ?? '-',
                    $row['program_studi'] ?? '-',
                    $row['semester'] ?? '-',
                    $row['regional_interview'] ?? '-',
                    $row['kota_pilihan'] ?? '-',
                    $row['divisi_pilihan'] ?? '-',
                    $row['jenis_magang'] ?? '-',
                    $row['status'] ?? '-',
                    $periodeMulai,
                    $periodeSelesai,
                    $tglDaftar,
                    $tglUbah,
                    $catatan
                ], ',', '"', "\\");
            }
        } else {
            fputcsv($output, [
                'No', 'Token Pendaftaran', 'Nama Lengkap', 'Divisi Pilihan', 'Asal Kampus',
                'Program Studi', 'Periode Mulai', 'Periode Selesai', 'Status Magang',
                'Suket Penerimaan', 'Suket Selesai', 'Sertif Selesai'
            ], ',', '"', "\\");

            $no = 1;
            foreach ($pendaftaran as $row) {
                $periodeMulai   = !empty($row['periode_mulai']) ? date('d/m/Y', strtotime($row['periode_mulai'])) : '-';
                $periodeSelesai = !empty($row['periode_selesai']) ? date('d/m/Y', strtotime($row['periode_selesai'])) : '-';
                $currentStatus  = $row['status'] ?? '-';
                $statusDisplay  = ($currentStatus === 'Diterima') ? 'Active' : (($currentStatus === 'Complete') ? 'Completed' : $currentStatus);

                fputcsv($output, [
                    $no++,
                    (string) ($row['token_pendaftaran'] ?? '-'),
                    $row['nama_lengkap'] ?? '-',
                    $row['divisi_pilihan'] ?? '-',
                    $row['asal_kampus'] ?? '-',
                    $row['program_studi'] ?? '-',
                    $periodeMulai,
                    $periodeSelesai,
                    $statusDisplay,
                    'Belum Diproses',
                    'Belum Diproses',
                    'Belum Diproses'
                ], ',', '"', "\\");
            }
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
            $this->pendaftaranModel->update($id, [
                'is_archived'     => 1,
                'archived_at'     => date('Y-m-d H:i:s'),
                'archived_reason' => 'Dihapus manual oleh admin',
            ]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'id' => (int) $id, 'message' => 'Data pendaftaran berhasil diarsipkan.']);
            }
            return redirect()->to('/admin/dashboard')->with('success', 'Data pendaftaran berhasil diarsipkan.');
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
            'proposal' => ['field' => 'proposal_magang', 'folder' => 'proposal', 'prefix' => 'Proposal_'],
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
        
        // Gunakan inline disposition agar file (PDF/Gambar) terbuka di browser untuk di-review
        $fileInfo = new \CodeIgniter\Files\File($filePath);
        $mime = $fileInfo->getMimeType();
        
        return $this->response
            ->setContentType($mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $downloadName . '"')
            ->setBody(file_get_contents($filePath));
    }

    /**
     * SATU endpoint serba-bisa, sengaja dibuat pakai route yang SUDAH ADA
     * (admin/process-interview/(:num)/(:segment)) supaya TIDAK perlu edit
     * Routes.php sama sekali. Dibedakan lewat isi $action:
     *   - 'info'   -> GET data lengkap 1 kandidat (buat isi modal aksi)
     *   - 'wa'     -> GET link WhatsApp siap kirim
     *   - 'email'  -> kirim email notifikasi sesuai status saat ini
     *   - selain itu -> dianggap status tujuan ('Menunggu', 'Progress',
     *                   'Diterima', atau 'Ditolak') dan akan mengubah status
     *                   kandidat.
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

        // Lazy auto-complete per-record: jika periode_selesai sudah lewat, ubah dulu sebelum aksi lain
        if (PendaftaranModel::shouldBeComplete($pendaftaran)) {
            $this->pendaftaranModel->update($id, ['status' => 'Complete', 'status_changed_at' => date('Y-m-d H:i:s')]);
            $pendaftaran = $this->pendaftaranModel->find($id);
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

        if ($action === 'edit_divisi_periode') {
            $divisi = $this->request->getPost('divisi_pilihan') ?? '';
            $mulai = $this->request->getPost('periode_mulai') ?? '';
            $selesai = $this->request->getPost('periode_selesai') ?? '';

            if (!$this->pendaftaranModel->update($id, [
                'divisi_pilihan' => $divisi,
                'periode_mulai' => $mulai,
                'periode_selesai' => $selesai
            ])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan perubahan divisi/periode.']);
            }
            return $this->response->setJSON(['success' => true, 'message' => 'Divisi dan Periode Magang berhasil diperbarui.']);
        }

        if (preg_match('/^schedule_interview_([12])$/', $action, $matches)) {
            if (!$this->request->is('post')) {
                return $this->jsonOrRedirect(false, 'Jadwal interview harus dikirim melalui POST.', 405);
            }

            $step = (int) $matches[1];
            $expectedStatus = $step === 1 ? 'Menunggu' : 'Progress';
            if ($pendaftaran['status'] !== $expectedStatus) {
                return $this->jsonOrRedirect(false, "Interview tahap {$step} tidak dapat dijadwalkan pada status kandidat saat ini.", 422);
            }

            $jadwal = trim((string) $this->request->getPost('jadwal'));
            $parsed = $this->parseJadwal($jadwal);
            if ($parsed === null) {
                return $this->jsonOrRedirect(false, 'Jadwal interview wajib diisi dengan format yang valid.', 422);
            }

            $data = [
                'jadwal_interview_' . $step => $parsed,
                'link_zoom_' . $step        => trim((string) $this->request->getPost('link_zoom')),
                'catatan_interview_' . $step=> trim((string) $this->request->getPost('catatan')),
            ];

            if (!$this->pendaftaranModel->update($id, $data)) {
                return $this->jsonOrRedirect(false, "Gagal menyimpan jadwal interview tahap {$step}.", 500);
            }

            $updated = $this->pendaftaranModel->find($id);
            return $this->response->setJSON(array_merge([
                'success'                     => true,
                'message'                     => "Jadwal Interview Tahap {$step} berhasil disimpan. Status kandidat tidak berubah.",
                'id'                          => (int) $id,
                'status'                      => $updated['status'],
                'badge_html'                  => $this->renderStatusBadge($updated),
                'should_prompt_notifications' => true,
            ], $this->itemPayload($updated)));
        }

        // Selain itu: $action adalah target status baru
        if (!$this->request->is('post')) {
            return $this->jsonOrRedirect(false, 'Perubahan status harus dikirim melalui POST.', 405);
        }

        return $this->handleSetStatus($id, $pendaftaran, $action);
    }

    private function handleSetStatus($id, array $pendaftaran, string $targetStatus)
    {
        if (!in_array($targetStatus, PendaftaranModel::statusList(), true)) {
            return $this->jsonOrRedirect(false, 'Status tujuan tidak dikenali: ' . $targetStatus, 422);
        }

        $isManual = ($this->request->getPost('is_manual') === '1') || ($this->request->getGet('is_manual') === '1');

        if (!$isManual) {
            $allowedTransitions = [
                'Menunggu' => ['Progress', 'Diterima', 'Ditolak'],
                'Progress' => ['Diterima', 'Ditolak'],
                'Diterima' => ['Complete', 'Ditolak'],
                'Complete' => ['Diterima', 'Ditolak'],
            ];

            if (isset($allowedTransitions[$pendaftaran['status']])
                && !in_array($targetStatus, $allowedTransitions[$pendaftaran['status']], true)
                && $targetStatus !== $pendaftaran['status']) {
                return $this->jsonOrRedirect(false, 'Transisi status tidak valid. Muat ulang data kandidat lalu coba lagi.', 422);
            }
        }

        $catatan  = trim((string) ($this->request->getGet('catatan') ?? $this->request->getPost('catatan') ?? ''));
        $kota     = trim((string) ($this->request->getPost('regional_interview') ?? ''));
        $kotaPilihan = trim((string) ($this->request->getPost('kota_pilihan') ?? $this->request->getPost('kota_magang') ?? ''));
        $divisi   = trim((string) ($this->request->getPost('divisi_pilihan') ?? ''));
        $mulai    = trim((string) ($this->request->getPost('periode_mulai') ?? ''));
        $selesai  = trim((string) ($this->request->getPost('periode_selesai') ?? ''));
        $nim      = trim((string) ($this->request->getPost('nim') ?? ''));
        $jadwal   = trim((string) ($this->request->getGet('jadwal') ?? $this->request->getPost('jadwal') ?? ''));
        $linkZoom = trim((string) ($this->request->getGet('link_zoom') ?? $this->request->getPost('link_zoom') ?? ''));

        $data = ['status' => $targetStatus];
        if ($targetStatus !== $pendaftaran['status']) {
            $data['status_changed_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->request->getPost('nim') !== null) {
            $data['nim'] = $nim !== '' ? $nim : null;
        }
        if ($catatan !== '') {
            $data['catatan_admin'] = $catatan;
        }
        if ($kota !== '') {
            if (!in_array($kota, ['Semarang', 'Surabaya', 'Bali'], true)) {
                return $this->jsonOrRedirect(false, 'Regional interview tidak valid.', 422);
            }
            $data['regional_interview'] = $kota;
        }
        if ($kotaPilihan !== '') {
            if (!in_array($kotaPilihan, (new \Config\InternshipLocations())->allKotaPilihan(), true)) {
                return $this->jsonOrRedirect(false, 'Kota pilihan tidak valid.', 422);
            }
            $data['kota_pilihan'] = $kotaPilihan;
        }
        if ($divisi !== '') {
            $data['divisi_pilihan'] = $divisi;
        }
        if ($mulai !== '') {
            $data['periode_mulai'] = $mulai;
        }
        if ($selesai !== '') {
            $data['periode_selesai'] = $selesai;
        }

        // --- Handle proposal file upload ---
        $proposalFile = $this->request->getFile('proposal_magang');
        if ($proposalFile && $proposalFile->isValid() && !$proposalFile->hasMoved()) {
            // Validate: PDF only, max 2MB
            if ($proposalFile->getClientMimeType() !== 'application/pdf') {
                return $this->jsonOrRedirect(false, 'File proposal harus berformat PDF.', 422);
            }
            if ($proposalFile->getSize() > 2 * 1024 * 1024) {
                return $this->jsonOrRedirect(false, 'Ukuran file proposal maksimal 2MB.', 422);
            }

            $uploadDir = WRITEPATH . 'uploads/proposal';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Delete old proposal file if exists
            if (!empty($pendaftaran['proposal_magang'])) {
                $oldFile = $uploadDir . '/' . $pendaftaran['proposal_magang'];
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $newName = $proposalFile->getRandomName();
            $proposalFile->move($uploadDir, $newName);
            $data['proposal_magang'] = $newName;
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
            'status_label' => $this->formatStatusLabel($this->displayStatus($updated)),
            'badge_html'   => $this->renderStatusBadge($updated),
            'aksi_html'    => $this->renderAksiCell($updated),
        ], $this->itemPayload($updated));

        // Pengiriman email status dari admin dilakukan manual (via pop-up konfirmasi atau tombol email)

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
            'proposal_magang' => 'proposal',
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
     * Otomatis ubah Diterima -> Complete jika periode_selesai sudah lewat.
     * Dijalankan setiap dashboard dibuka + via cron, jadi tidak perlu manual.
     */
    private function runAutoComplete(): void
    {
        $affected = $this->pendaftaranModel->autoCompleteExpired();
        if ($affected > 0) {
            log_message('info', "AutoComplete: {$affected} kandidat Diterima -> Complete (periode_selesai lewat).");
        }
    }

    /**
     * Kebijakan retensi data, 2 tahap (dijalankan otomatis tiap kali dashboard
     * dibuka, atau lewat cron `php spark cleanup:pendaftaran`):
     *
     *  TAHAP 1 - masuk ARSIP dulu (belum dihapus, cuma disembunyikan dari
     *  tabel utama & bisa dipulihkan admin) jika tidak ada perubahan selama 2 minggu (based on updated_at)
     *
     *  TAHAP 2 - HAPUS PERMANEN / Sembunyikan dari arsip jika tidak ada perubahan selama 3 minggu (21 hari)
     *  berdasarkan updated_at (atau 7 hari setelah masuk arsip).
     */
    private function runRetentionCleanup(): void
    {
        $twoWeeksAgo   = date('Y-m-d H:i:s', strtotime('-14 days'));
        $threeWeeksAgo = date('Y-m-d H:i:s', strtotime('-21 days'));

        // ---- TAHAP 1: masuk arsip jika tidak ada perubahan selama 2 minggu ----
        $inactive = $this->pendaftaranModel
            ->where('is_archived', 0)
            ->where('COALESCE(updated_at, created_at) <=', $twoWeeksAgo)
            ->findAll();
        $this->archiveCandidates($inactive, 'Tidak ada perubahan (2 minggu)');

        // ---- TAHAP 2: hapus permanen jika 3 minggu tidak ada perubahan / 7 hari di arsip ----
        $expired = $this->pendaftaranModel
            ->where('is_archived', 1)
            ->groupStart()
                ->where('COALESCE(updated_at, created_at) <=', $threeWeeksAgo)
                ->orWhere('archived_at <=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->groupEnd()
            ->findAll();
        $this->purgeCandidates($expired, 'tidak ada perubahan 3 minggu / 7 hari di arsip');
    }

    private function archiveCandidates(array $rows, string $reason): void
    {
        foreach ($rows as $row) {
            if (in_array($row['status'], ['Diterima', 'Complete'], true)) {
                log_message('info', "Auto-arsip dilewati untuk kandidat #{$row['id']} ({$row['nama_lengkap']}) karena status {$row['status']}.");
                continue;
            }
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
            $this->pendaftaranModel->update($row['id'], [
                'is_archived'     => 1,
                'archived_at'     => date('Y-m-d H:i:s'),
                'archived_reason' => $reason,
            ]);
            log_message('info', "Arsipkan kandidat #{$row['id']} ({$row['nama_lengkap']}) - {$reason}");
        }
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
            'id', 'nama_lengkap', 'email', 'nomor_whatsapp', 'nim', 'status',
            'jadwal_interview_1', 'jadwal_interview_2', 'jadwal_interview_3',
            'link_zoom_1', 'link_zoom_2', 'link_zoom_3',
            'catatan_interview_1', 'catatan_interview_2', 'catatan_interview_3',
            'catatan_admin', 'email_terkirim',
            'is_archived', 'archived_at', 'archived_reason',
            'regional_interview', 'kota_pilihan', 'divisi_pilihan', 'periode_mulai', 'periode_selesai',
            'proposal_magang'
        ];
        foreach ($fields as $f) {
            $out['item'][$f] = $item[$f] ?? null;
        }
        return $out;
    }

    private function statusBadgeClass(string $status): string
    {
        return match (true) {
            in_array($status, ['Diterima'], true) => 'bg-success',
            $status === 'Complete' => 'bg-purple',
            $status === 'Ditolak' => 'bg-danger',
            in_array($status, ['Progress', 'Interview Tahap 1', 'Interview Tahap 2'], true) => 'bg-info',
            default => 'bg-warning',
        };
    }

    private function displayStatus(array $item): string
    {
        $status = $item['status'] ?? 'Menunggu';
        if (empty($status)) {
            $status = 'Menunggu';
        }
        if ($status === 'Menunggu' && !empty($item['jadwal_interview_1'])) {
            return 'Interview Tahap 1';
        }
        if ($status === 'Progress' && !empty($item['jadwal_interview_2'])) {
            return 'Interview Tahap 2';
        }
        return $status;
    }

    private function formatStatusLabel(?string $status): string
    {
        if (empty($status)) {
            return 'MENUNGGU';
        }
        return strtoupper(str_replace('_', ' ', $status));
    }

    private function renderStatusBadge(array $item): string
    {
        $status = $this->displayStatus($item);
        $class = $this->statusBadgeClass($status);

        $html = '<span class="badge ' . $class . '">' . esc($this->formatStatusLabel($status)) . '</span>';

        $step = $status === 'Interview Tahap 1' ? 1 : ($status === 'Interview Tahap 2' ? 2 : 0);
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

            $email->setFrom('farezaairo@gmail.com', 'Industry-Academia Collaboration Program');
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
        return InterviewNotificationService::buildEmailTemplate($item);
    }

    private function buildWaTemplate(array $item): array
    {
        return InterviewNotificationService::buildWaTemplate($item);
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


    public function analyzeProposal($id)
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $pendaftaran = $this->pendaftaranModel->find($id);
        if (!$pendaftaran || empty($pendaftaran['proposal_magang'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Proposal tidak ditemukan.']);
        }

        $filePath = WRITEPATH . 'uploads/proposal/' . $pendaftaran['proposal_magang'];
        if (!file_exists($filePath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'File proposal tidak ditemukan di server.']);
        }

        $service = new \App\Services\ProposalAnalysisService();
        $result = $service->analyze($filePath, $pendaftaran['divisi_pilihan']);

        return $this->response->setJSON($result);
    }

    /**
     * Download single certificate as PPTX
     */
    public function downloadCertificatePptx($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $candidate = $this->pendaftaranModel->find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $nama = !empty($candidate['nama_lengkap']) ? trim($candidate['nama_lengkap']) : ('Peserta_' . $candidate['id']);
        $cleanName = preg_replace('/[^\p{L}\p{N}\s_\-]/u', '', $nama);
        $fileName = "Sertifikat Selesai Industry-Academia Collaboration Program_{$cleanName}.pptx";

        // Jika parameter download=1 diset, kirim langsung sebagai attachment file
        if ($this->request->getGet('download') === '1') {
            try {
                $service = new \App\Services\CertificateService();
                $binary = $service->generatePptxString($candidate);
                return $this->response->download($fileName, $binary);
            } catch (\Throwable $e) {
                log_message('error', 'Certificate PPTX Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat PPTX: ' . $e->getMessage());
            }
        }

        // Tampilkan halaman pratinjau di tab baru dengan opsi download
        return view('admin/preview_page', [
            'docType'     => 'pptx',
            'docTitle'    => 'Sertifikat Selesai Program Magang',
            'badgeTitle'  => 'PowerPoint (.pptx)',
            'badgeClass'  => 'bg-warning text-dark',
            'iconClass'   => 'bi-file-earmark-ppt-fill',
            'btnClass'    => 'btn-warning text-dark',
            'fileName'    => $fileName,
            'downloadUrl' => site_url("admin/certificate/pptx/{$id}?download=1"),
            'inlineUrl'   => site_url("admin/certificate/pdf/{$id}?inline=1"),
            'candidate'   => $candidate,
        ]);
    }

    /**
     * Download single certificate as PDF (Pure PHP Engine in memory)
     */
    public function downloadCertificatePdf($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $candidate = $this->pendaftaranModel->find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $nama = !empty($candidate['nama_lengkap']) ? trim($candidate['nama_lengkap']) : ('Peserta_' . $candidate['id']);
        $cleanName = preg_replace('/[^\p{L}\p{N}\s_\-]/u', '', $nama);
        $fileName = "Sertifikat Selesai Industry-Academia Collaboration Program_{$cleanName}.pdf";

        // Jika parameter download=1 diset, kirim langsung sebagai attachment file
        if ($this->request->getGet('download') === '1') {
            try {
                $service = new \App\Services\CertificateService();
                $binary = $service->generatePdfString($candidate);
                return $this->response->download($fileName, $binary);
            } catch (\Throwable $e) {
                log_message('error', 'Certificate PDF Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        // Jika parameter inline=1 diset, tampilkan PDF secara native di browser
        if ($this->request->getGet('inline') === '1') {
            try {
                $service = new \App\Services\CertificateService();
                $binary = $service->generatePdfString($candidate);
                return $this->response
                    ->setContentType('application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                    ->setBody($binary);
            } catch (\Throwable $e) {
                log_message('error', 'Certificate PDF Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        // Tampilkan halaman pratinjau di tab baru dengan opsi download
        return view('admin/preview_page', [
            'docType'     => 'pdf',
            'docTitle'    => 'Sertifikat Selesai Program Magang',
            'badgeTitle'  => 'PDF Document (.pdf)',
            'badgeClass'  => 'bg-danger text-white',
            'iconClass'   => 'bi-file-earmark-pdf-fill',
            'btnClass'    => 'btn-danger text-white',
            'fileName'    => $fileName,
            'downloadUrl' => site_url("admin/certificate/pdf/{$id}?download=1"),
            'inlineUrl'   => site_url("admin/certificate/pdf/{$id}?inline=1"),
            'candidate'   => $candidate,
        ]);
    }

    /**
     * Download Surat Penerimaan as Word (.docx)
     */
    public function downloadSuratPenerimaan($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $candidate = $this->pendaftaranModel->find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $cleanName = preg_replace('/[^\p{L}\p{N}\s_\-]/u', '', $candidate['nama_lengkap'] ?? ('Peserta_' . $id));
        $fileName = "Surat Penerimaan Industry-Academia Collaboration Program_{$cleanName}.docx";
        $pdfFileName = "Surat Penerimaan Industry-Academia Collaboration Program_{$cleanName}.pdf";

        // Jika parameter download=1 diset, kirim langsung sebagai attachment Word (.docx)
        if ($this->request->getGet('download') === '1') {
            try {
                $service = new \App\Services\SuratService();
                $binary = $service->generateSuratPenerimaanString($candidate);
                return $this->response->download($fileName, $binary);
            } catch (\Throwable $e) {
                log_message('error', 'Surat Penerimaan Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat surat: ' . $e->getMessage());
            }
        }

        // Jika parameter download_pdf=1 diset, kirim langsung sebagai attachment PDF (.pdf)
        if ($this->request->getGet('download_pdf') === '1') {
            try {
                $service = new \App\Services\SuratService();
                $binary = $service->generatePdfString($candidate, 'penerimaan');
                return $this->response->download($pdfFileName, $binary);
            } catch (\Throwable $e) {
                log_message('error', 'Surat Penerimaan PDF Download Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        // Jika parameter inline=1 diset, alirkan PDF secara native untuk preview di browser
        if ($this->request->getGet('inline') === '1') {
            try {
                $service = new \App\Services\SuratService();
                $binary = $service->generatePdfString($candidate, 'penerimaan');
                return $this->response
                    ->setContentType('application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $pdfFileName . '"')
                    ->setBody($binary);
            } catch (\Throwable $e) {
                log_message('error', 'Surat Penerimaan PDF Inline Error: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setBody('Gagal membuat pratinjau: ' . $e->getMessage());
            }
        }

        // Tampilkan halaman pratinjau terpadu di tab baru dengan viewer PDF native
        return view('admin/preview_page', [
            'docType'        => 'docx',
            'docTitle'       => 'Surat Penerimaan Magang',
            'badgeTitle'     => 'Microsoft Word (.docx)',
            'badgeClass'     => 'bg-primary text-white',
            'iconClass'      => 'bi-file-earmark-word-fill',
            'btnClass'       => 'btn-primary text-white',
            'fileName'       => $fileName,
            'downloadUrl'    => site_url("admin/surat/penerimaan/{$id}?download=1"),
            'downloadPdfUrl' => site_url("admin/surat/penerimaan/{$id}?download_pdf=1"),
            'inlineUrl'      => site_url("admin/surat/penerimaan/{$id}?inline=1"),
            'candidate'      => $candidate,
        ]);
    }

    /**
     * Download Surat Keterangan Selesai as Word (.docx)
     */
    public function downloadSuratKeteranganSelesai($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $candidate = $this->pendaftaranModel->find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $cleanName = preg_replace('/[^\p{L}\p{N}\s_\-]/u', '', $candidate['nama_lengkap'] ?? ('Peserta_' . $id));
        $fileName = "Surat Keterangan Selesai Industry-Academia Collaboration Program_{$cleanName}.docx";
        $pdfFileName = "Surat Keterangan Selesai Industry-Academia Collaboration Program_{$cleanName}.pdf";

        // Jika parameter download=1 diset, kirim langsung sebagai attachment Word (.docx)
        if ($this->request->getGet('download') === '1') {
            try {
                $service = new \App\Services\SuratService();
                $binary = $service->generateSuratKeteranganSelesaiString($candidate);
                return $this->response->download($fileName, $binary);
            } catch (\Throwable $e) {
                log_message('error', 'Surat Keterangan Selesai Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat surat: ' . $e->getMessage());
            }
        }

        // Jika parameter download_pdf=1 diset, kirim langsung sebagai attachment PDF (.pdf)
        if ($this->request->getGet('download_pdf') === '1') {
            try {
                $service = new \App\Services\SuratService();
                $binary = $service->generatePdfString($candidate, 'selesai');
                return $this->response->download($pdfFileName, $binary);
            } catch (\Throwable $e) {
                log_message('error', 'Surat Selesai PDF Download Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
            }
        }

        // Jika parameter inline=1 diset, alirkan PDF secara native untuk preview di browser
        if ($this->request->getGet('inline') === '1') {
            try {
                $service = new \App\Services\SuratService();
                $binary = $service->generatePdfString($candidate, 'selesai');
                return $this->response
                    ->setContentType('application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $pdfFileName . '"')
                    ->setBody($binary);
            } catch (\Throwable $e) {
                log_message('error', 'Surat Selesai PDF Inline Error: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setBody('Gagal membuat pratinjau: ' . $e->getMessage());
            }
        }

        // Tampilkan halaman pratinjau terpadu di tab baru dengan viewer PDF native
        return view('admin/preview_page', [
            'docType'        => 'docx',
            'docTitle'       => 'Surat Keterangan Selesai Magang',
            'badgeTitle'     => 'Microsoft Word (.docx)',
            'badgeClass'     => 'bg-info text-white',
            'iconClass'      => 'bi-file-earmark-word-fill',
            'btnClass'       => 'btn-info text-white',
            'fileName'       => $fileName,
            'downloadUrl'    => site_url("admin/surat/selesai/{$id}?download=1"),
            'downloadPdfUrl' => site_url("admin/surat/selesai/{$id}?download_pdf=1"),
            'inlineUrl'      => site_url("admin/surat/selesai/{$id}?inline=1"),
            'candidate'      => $candidate,
        ]);
    }
}
