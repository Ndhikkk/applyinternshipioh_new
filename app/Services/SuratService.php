<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use DOMXPath;

class SuratService
{
    protected string $suratPenerimaanTemplatePath;
    protected string $suratKeteranganSelesaiTemplatePath;
    protected string $assetsPath;

    public function __construct()
    {
        $candidatesPenerimaan = [
            APPPATH . 'ThirdParty/surat-penerimaan/Surat Penerimaan Industry-Academia Collaboration Program.docx',
            WRITEPATH . 'templates/surat-penerimaan/Surat Penerimaan Industry-Academia Collaboration Program.docx',
            ROOTPATH . 'writable/templates/surat-penerimaan/Surat Penerimaan Industry-Academia Collaboration Program.docx',
            APPPATH . '../writable/templates/surat-penerimaan/Surat Penerimaan Industry-Academia Collaboration Program.docx',
        ];

        $this->suratPenerimaanTemplatePath = '';
        foreach ($candidatesPenerimaan as $p) {
            if (file_exists($p)) {
                $this->suratPenerimaanTemplatePath = $p;
                break;
            }
        }

        if (!$this->suratPenerimaanTemplatePath) {
            $this->suratPenerimaanTemplatePath = APPPATH . 'ThirdParty/surat-penerimaan/Surat Penerimaan Industry-Academia Collaboration Program.docx';
        }

        $candidatesSelesai = [
            APPPATH . 'ThirdParty/surat-keterangan-selesai/Surat Keterangan Selesai Industry-Academia Collaboration Program.docx',
            WRITEPATH . 'templates/surat-keterangan-selesai/Surat Keterangan Selesai Industry-Academia Collaboration Program.docx',
            ROOTPATH . 'writable/templates/surat-keterangan-selesai/Surat Keterangan Selesai Industry-Academia Collaboration Program.docx',
            APPPATH . '../writable/templates/surat-keterangan-selesai/Surat Keterangan Selesai Industry-Academia Collaboration Program.docx',
        ];

        $this->suratKeteranganSelesaiTemplatePath = '';
        foreach ($candidatesSelesai as $p) {
            if (file_exists($p)) {
                $this->suratKeteranganSelesaiTemplatePath = $p;
                break;
            }
        }

        if (!$this->suratKeteranganSelesaiTemplatePath) {
            $this->suratKeteranganSelesaiTemplatePath = APPPATH . 'ThirdParty/surat-keterangan-selesai/Surat Keterangan Selesai Industry-Academia Collaboration Program.docx';
        }

        $this->assetsPath = APPPATH . 'ThirdParty/surat/';
        if (!is_dir($this->assetsPath)) {
            @mkdir($this->assetsPath, 0755, true);
        }
        $this->ensureAssetsExist();
    }

    /**
     * Generate Surat Penerimaan as binary string in memory
     */
    public function generateSuratPenerimaanString(array $data): string
    {
        if (!file_exists($this->suratPenerimaanTemplatePath)) {
            throw new \RuntimeException('Template surat penerimaan tidak ditemukan di: ' . $this->suratPenerimaanTemplatePath);
        }

        $periodeMulaiStr   = $this->formatIndonesianDate($data['periode_mulai'] ?? null, '-');
        $periodeSelesaiStr = $this->formatIndonesianDate($data['periode_selesai'] ?? null, '-');
        $tanggalTerbit     = $this->formatIndonesianDate(date('Y-m-d'), date('d F Y'));

        // Compute durasi in months (matches 'selama [durasi] bulan' in template)
        $durasi = '-';
        if (!empty($data['periode_mulai']) && !empty($data['periode_selesai'])) {
            try {
                $start = new \DateTime($data['periode_mulai']);
                $end   = new \DateTime($data['periode_selesai']);
                $diff  = $start->diff($end);
                $months = ($diff->y * 12) + $diff->m;
                if ($diff->d >= 15) {
                    $months += 1;
                }
                $durasi = (string) max(1, $months);
            } catch (\Throwable $e) {
                $durasi = '-';
            }
        }

        $rawKota = !empty($data['kota_pilihan']) 
            ? trim($data['kota_pilihan']) 
            : (!empty($data['kota_magang']) 
                ? trim($data['kota_magang']) 
                : (!empty($data['regional_interview']) 
                    ? trim($data['regional_interview']) 
                    : 'Semarang'));
        $kotaPilihan = $this->cleanKotaName($rawKota);

        $replacements = [
            '[nama_lengkap]'        => !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : '-',
            '[nim]'                 => !empty($data['nim']) ? trim($data['nim']) : '-',
            '[asal_kampus]'         => !empty($data['asal_kampus']) ? trim($data['asal_kampus']) : '-',
            '[program_studi]'       => !empty($data['program_studi']) ? trim($data['program_studi']) : '-',
            '[durasi]'              => $durasi,
            '[periode_mulai]'       => $periodeMulaiStr,
            '[periode_selesai]'     => $periodeSelesaiStr,
            '[divisi_pilihan]'      => !empty($data['divisi_pilihan']) ? trim($data['divisi_pilihan']) : '-',
            '[kota_pilihan]'        => $kotaPilihan,
            '[tanggal penerbitan]'  => $tanggalTerbit,
            '[tanggal_penerbitan]'  => $tanggalTerbit,
        ];

        return $this->renderDocx($this->suratPenerimaanTemplatePath, $replacements);
    }

    /**
     * Generate Surat Keterangan Selesai as binary string in memory
     */
    public function generateSuratKeteranganSelesaiString(array $data): string
    {
        if (!file_exists($this->suratKeteranganSelesaiTemplatePath)) {
            throw new \RuntimeException('Template surat keterangan selesai tidak ditemukan di: ' . $this->suratKeteranganSelesaiTemplatePath);
        }

        $periodeMulaiStr   = $this->formatIndonesianDate($data['periode_mulai'] ?? null, '-');
        $periodeSelesaiStr = $this->formatIndonesianDate($data['periode_selesai'] ?? null, '-');
        $tanggalTerbit     = $this->formatIndonesianDate(date('Y-m-d'), date('d F Y'));

        $rawKota = !empty($data['kota_pilihan']) 
            ? trim($data['kota_pilihan']) 
            : (!empty($data['kota_magang']) 
                ? trim($data['kota_magang']) 
                : (!empty($data['regional_interview']) 
                    ? trim($data['regional_interview']) 
                    : 'Semarang'));
        $kotaPilihan = $this->cleanKotaName($rawKota);

        $replacements = [
            '[nama_lengkap]'        => !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : '-',
            '[nim]'                 => !empty($data['nim']) ? trim($data['nim']) : '-',
            '[asal_kampus]'         => !empty($data['asal_kampus']) ? trim($data['asal_kampus']) : '-',
            '[program_studi]'       => !empty($data['program_studi']) ? trim($data['program_studi']) : '-',
            '[divisi_pilihan]'      => !empty($data['divisi_pilihan']) ? trim($data['divisi_pilihan']) : '-',
            '[periode_mulai]'       => $periodeMulaiStr,
            '[periode_selesai]'     => $periodeSelesaiStr,
            '[kota_pilihan]'        => $kotaPilihan,
            '[tanggal_penerbitan]'  => $tanggalTerbit,
            '[tanggal penerbitan]'  => $tanggalTerbit,
        ];

        return $this->renderDocx($this->suratKeteranganSelesaiTemplatePath, $replacements);
    }

    /**
     * Render a .docx template with placeholder replacements, return binary string.
     */
    protected function renderDocx(string $templatePath, array $replacements): string
    {
        $tempFile = sys_get_temp_dir() . '/surat_' . uniqid('', true) . '.docx';
        copy($templatePath, $tempFile);

        $zip = new ZipArchive();
        if ($zip->open($tempFile) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml) {
                $dom = new DOMDocument();
                @$dom->loadXML($xml);
                $xpath = new DOMXPath($dom);
                $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                $paragraphs = $xpath->query('//w:p');
                foreach ($paragraphs as $p) {
                    $tNodes = $xpath->query('.//w:t', $p);
                    if ($tNodes->length === 0) continue;

                    // Build full text and map each character index to its DOM text node
                    $fullText = '';
                    $nodeMap = [];
                    foreach ($tNodes as $t) {
                        $val = $t->nodeValue;
                        $len = mb_strlen($val, 'UTF-8');
                        for ($i = 0; $i < $len; $i++) {
                            $nodeMap[] = ['node' => $t, 'offset' => $i];
                        }
                        $fullText .= $val;
                    }

                    foreach ($replacements as $search => $replaceVal) {
                        $pos = mb_strpos($fullText, $search, 0, 'UTF-8');
                        while ($pos !== false) {
                            $searchLen = mb_strlen($search, 'UTF-8');

                            $firstMatch = $nodeMap[$pos];
                            $firstNode = $firstMatch['node'];

                            // Collect all nodes involved in this placeholder match
                            $nodesInvolved = [];
                            for ($j = $pos; $j < $pos + $searchLen; $j++) {
                                $n = $nodeMap[$j]['node'];
                                if (!in_array($n, $nodesInvolved, true)) {
                                    $nodesInvolved[] = $n;
                                }
                            }

                            // Replace placeholder text without touching surrounding labels or tabs
                            $escapedVal = htmlspecialchars($replaceVal, ENT_XML1, 'UTF-8');
                            if (count($nodesInvolved) === 1) {
                                $firstNode->nodeValue = str_replace($search, $escapedVal, $firstNode->nodeValue);
                            } else {
                                $firstNode->nodeValue = $escapedVal;
                                for ($k = 1; $k < count($nodesInvolved); $k++) {
                                    $nodesInvolved[$k]->nodeValue = '';
                                }
                            }

                            // Refresh mapping for any subsequent replacements in the same paragraph
                            $fullText = '';
                            $nodeMap = [];
                            foreach ($tNodes as $t) {
                                $val = $t->nodeValue;
                                $len = mb_strlen($val, 'UTF-8');
                                for ($i = 0; $i < $len; $i++) {
                                    $nodeMap[] = ['node' => $t, 'offset' => $i];
                                }
                                $fullText .= $val;
                            }

                            $pos = mb_strpos($fullText, $search, 0, 'UTF-8');
                        }
                    }
                }

                $zip->addFromString('word/document.xml', $dom->saveXML());
            }
            $zip->close();
        }

        $binary = file_get_contents($tempFile);
        unlink($tempFile);
        return $binary;
    }

    /**
     * Format date to Indonesian (e.g. 13 April 2026)
     */
    public function formatIndonesianDate(?string $dateStr, string $fallback = ''): string
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            return $fallback;
        }

        $time = strtotime($dateStr);
        if (!$time) {
            return $fallback;
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return date('j', $time) . ' ' . ($months[(int) date('n', $time)] ?? date('F', $time)) . ' ' . date('Y', $time);
    }

    /**
     * Clean city name by stripping prefixes like "Kota", "Kabupaten", "Kab.", "Kab"
     */
    public function cleanKotaName(?string $kota): string
    {
        if (empty($kota)) {
            return 'Semarang';
        }

        // Strip prefixes: "Kota Administrasi ", "Kota Adm. ", "Kota ", "Kabupaten ", "Kab. ", "Kab "
        $cleaned = preg_replace('/^(?:kota\s+administrasi\s+|kota\s+adm\.?\s+|kota\s+|kabupaten\s+|kab\.?\s+)/iu', '', trim($kota));
        $cleaned = trim($cleaned);

        return !empty($cleaned) ? $cleaned : trim($kota);
    }

    /**
     * Calculate internship duration in months
     */
    public function calculateDuration(?string $mulai, ?string $selesai): string
    {
        if (empty($mulai) || empty($selesai)) {
            return '-';
        }
        try {
            $start = new \DateTime($mulai);
            $end   = new \DateTime($selesai);
            $diff  = $start->diff($end);
            $months = ($diff->y * 12) + $diff->m;
            if ($diff->d >= 15) {
                $months += 1;
            }
            return (string) max(1, $months);
        } catch (\Throwable $e) {
            return '-';
        }
    }

    /**
     * Ensure graphic assets exist in ThirdParty/surat. If missing, auto-extract from docx template.
     */
    protected function ensureAssetsExist(): void
    {
        $required = ['logo_ioh.png', 'signature_restu.jpg', 'stamp_ioh.png', 'bg_circles.png'];
        $missing = false;
        foreach ($required as $req) {
            if (!file_exists($this->assetsPath . $req)) {
                $missing = true;
                break;
            }
        }

        if ($missing && file_exists($this->suratPenerimaanTemplatePath)) {
            $zip = new ZipArchive();
            if ($zip->open($this->suratPenerimaanTemplatePath) === true) {
                $map = [
                    'word/media/image1.png' => $this->assetsPath . 'logo_ioh.png',
                    'word/media/image2.jpg' => $this->assetsPath . 'signature_restu.jpg',
                    'word/media/image3.png' => $this->assetsPath . 'stamp_ioh.png',
                    'word/media/image4.png' => $this->assetsPath . 'bg_circles.png',
                ];
                foreach ($map as $entry => $dest) {
                    if (!file_exists($dest)) {
                        $content = $zip->getFromName($entry);
                        if ($content !== false) {
                            file_put_contents($dest, $content);
                        }
                    }
                }
                $zip->close();
            }
        }
    }

    /**
     * Generate PDF version of Surat Penerimaan or Surat Keterangan Selesai
     * Pure PHP using FPDF - 100% reliable, zero external binaries, pixel-accurate.
     *
     * @param array $data Candidate details
     * @param string $type 'penerimaan' or 'selesai'
     * @return string Binary content of the PDF
     */
    public function generatePdfString(array $data, string $type = 'penerimaan'): string
    {
        if (!class_exists('\\FPDF')) {
            if (file_exists(APPPATH . 'ThirdParty/fpdf/fpdf.php')) {
                require_once APPPATH . 'ThirdParty/fpdf/fpdf.php';
            } elseif (defined('ROOTPATH') && file_exists(ROOTPATH . 'vendor/setasign/fpdf/fpdf.php')) {
                require_once ROOTPATH . 'vendor/setasign/fpdf/fpdf.php';
            }
        }

        $this->ensureAssetsExist();

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(30, 25, 30);
        $pdf->AddPage();

        // 1. Watermark circles bottom right
        $bgCircles = $this->assetsPath . 'bg_circles.png';
        if (file_exists($bgCircles)) {
            $pdf->Image($bgCircles, 125, 177, 85, 120);
        }

        // 2. Header logo top left
        $logo = $this->assetsPath . 'logo_ioh.png';
        if (file_exists($logo)) {
            $pdf->Image($logo, 30, 20, 48);
        }

        $pdf->SetFont('Times', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);

        // Judul Surat
        $pdf->SetY(52);
        $title = ($type === 'selesai')
            ? 'SURAT KETERANGAN SELESAI INDUSTRY-ACADEMIA COLLABORATION PROGRAM'
            : 'SURAT KETERANGAN INDUSTRY-ACADEMIA COLLABORATION PROGRAM';
        $pdf->Cell(150, 6, $title, 0, 1, 'C');
        $pdf->Ln(5);

        // Pembuka
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(150, 5, 'Yang bertanda tangan dibawah ini :', 0, 1, 'L');
        $pdf->Ln(1);

        $unitKerja = ($type === 'selesai')
            ? 'Capability Building & Training Circle Java'
            : 'Capability Building Circle Java';

        $pdf->Cell(32, 5.2, 'Nama', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, 'Restu Aneka Setiansyah', 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'NIK', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, '87136654', 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'Unit Kerja', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, $unitKerja, 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'Divisi', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, 'Sales Effectiveness Java', 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'Lokasi Kerja', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, 'Surabaya', 0, 1, 'L');
        $pdf->Ln(4);

        $pdf->Cell(150, 5, 'Menerangkan dengan sebenarnya bahwa :', 0, 1, 'L');
        $pdf->Ln(1);

        $namaLengkap  = !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : '-';
        $nim          = !empty($data['nim']) ? trim($data['nim']) : '-';
        $asalKampus   = !empty($data['asal_kampus']) ? trim($data['asal_kampus']) : '-';
        $programStudi = !empty($data['program_studi']) ? trim($data['program_studi']) : '-';
        $divisi       = !empty($data['divisi_pilihan']) ? trim($data['divisi_pilihan']) : '-';
        $mulai        = $this->formatIndonesianDate($data['periode_mulai'] ?? null, '-');
        $selesai      = $this->formatIndonesianDate($data['periode_selesai'] ?? null, '-');
        $durasi       = $this->calculateDuration($data['periode_mulai'] ?? null, $data['periode_selesai'] ?? null);

        $rawKota = !empty($data['kota_pilihan']) 
            ? trim($data['kota_pilihan']) 
            : (!empty($data['kota_magang']) 
                ? trim($data['kota_magang']) 
                : (!empty($data['regional_interview']) 
                    ? trim($data['regional_interview']) 
                    : 'Semarang'));
        $kotaPilihan = $this->cleanKotaName($rawKota);
        $tanggalTerbit = $this->formatIndonesianDate(date('Y-m-d'), date('d F Y'));

        $pdf->Cell(32, 5.2, 'Nama', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); 
        $pdf->Cell(113, 5.2, $namaLengkap, 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'NIM', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, $nim, 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'Universitas', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, $asalKampus, 0, 1, 'L');
        $pdf->Cell(32, 5.2, 'Jurusan', 0, 0, 'L'); $pdf->Cell(5, 5.2, ':', 0, 0, 'C'); $pdf->Cell(113, 5.2, $programStudi, 0, 1, 'L');
        $pdf->Ln(4);

        if ($type === 'penerimaan') {
            $isi = "Telah diterima mengikuti program Industry-Academia Collaboration Program di Indosat Ooredoo Hutchison selama {$durasi} bulan, terhitung dari tanggal {$mulai} - {$selesai} di Divisi/Departemen : {$divisi}.";
            $pdf->MultiCell(150, 5.5, $isi, 0, 'J');
            $pdf->Ln(3.5);

            $isi2 = "Surat keterangan ini dibuat agar dapat dipergunakan dengan semestinya.\nTerima kasih atas perhatian dan kerjasamanya.";
            $pdf->MultiCell(150, 5.5, $isi2, 0, 'J');
        } else {
            $isi = "Telah selesai melaksanakan program magang di PT. Indosat Ooredoo Hutchison, terhitung dari tanggal {$mulai} sampai {$selesai}, di Divisi/Departemen : {$divisi}.";
            $pdf->MultiCell(150, 5.5, $isi, 0, 'J');
            $pdf->Ln(3.5);

            $isi2 = "Selama melaksanakan kegiatan Industry-Academia Collaboration Program, yang bersangkutan telah melaksanakan tugas dan tanggung jawabnya dengan baik. Surat keterangan ini dibuat agar dapat dipergunakan dengan semestinya.";
            $pdf->MultiCell(150, 5.5, $isi2, 0, 'J');
            $pdf->Ln(3.5);

            $isi3 = "Terima kasih atas perhatian dan kerjasamanya.";
            $pdf->MultiCell(150, 5.5, $isi3, 0, 'J');
        }

        $pdf->Ln(6);

        // Tanggal & Tanda Tangan
        $pdf->SetX(30);
        $pdf->Cell(150, 5.2, "{$kotaPilihan}, {$tanggalTerbit}", 0, 1, 'L');
        $pdf->Cell(150, 5.2, 'Hormat kami,', 0, 1, 'L');

        $sigY = $pdf->GetY() + 2;
        $sig = $this->assetsPath . 'signature_restu.jpg';
        if (file_exists($sig)) {
            $pdf->Image($sig, 30, $sigY, 32);
        }

        $stamp = $this->assetsPath . 'stamp_ioh.png';
        if (file_exists($stamp)) {
            $pdf->Image($stamp, 58, $sigY + 8, 22);
        }

        $pdf->SetY($sigY + 23);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(150, 5, 'Restu Aneka Setiansyah', 0, 1, 'L');
        $pdf->SetFont('Times', '', 10.5);
        $pdf->Cell(150, 4.5, $unitKerja, 0, 1, 'L');
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(150, 4.5, 'www.ioh.co.id', 0, 1, 'L');

        return $pdf->Output('S');
    }
}
