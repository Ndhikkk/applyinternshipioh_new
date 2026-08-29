<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use DOMXPath;

class CertificateService
{
    protected string $pptxTemplatePath;
    protected string $assetsPath;
    protected string $bgPath;
    protected string $logoPath;
    protected string $linePath;
    protected string $signaturePath;

    public function __construct()
    {
        // 1. Template PPTX path resolution
        $pptxCandidates = [
            APPPATH . 'ThirdParty/certificate/Sertifikat Selesai Industry-Academia Collaboration Program.pptx',
            WRITEPATH . 'templates/certificate/Sertifikat Selesai Industry-Academia Collaboration Program.pptx',
            ROOTPATH . 'writable/templates/certificate/Sertifikat Selesai Industry-Academia Collaboration Program.pptx',
            APPPATH . '../writable/templates/certificate/Sertifikat Selesai Industry-Academia Collaboration Program.pptx',
        ];
        $this->pptxTemplatePath = '';
        foreach ($pptxCandidates as $p) {
            if (file_exists($p)) {
                $this->pptxTemplatePath = $p;
                break;
            }
        }
        if (!$this->pptxTemplatePath) {
            $this->pptxTemplatePath = WRITEPATH . 'templates/certificate/Sertifikat Selesai Industry-Academia Collaboration Program.pptx';
        }

        // 2. Image Assets path resolution
        $imgDirCandidates = [
            APPPATH . 'ThirdParty/certificate/',
            defined('FCPATH') ? FCPATH . 'assets/img/certificate/' : '',
            defined('ROOTPATH') ? ROOTPATH . 'public/assets/img/certificate/' : '',
            defined('ROOTPATH') ? ROOTPATH . 'assets/img/certificate/' : '',
            APPPATH . '../public/assets/img/certificate/',
        ];
        $this->assetsPath = '';
        foreach ($imgDirCandidates as $dir) {
            if (!empty($dir) && is_dir($dir) && file_exists($dir . 'bg_certificate.jpg')) {
                $this->assetsPath = rtrim($dir, '/') . '/';
                break;
            }
        }
        if (!$this->assetsPath) {
            $this->assetsPath = APPPATH . 'ThirdParty/certificate/';
        }

        $this->bgPath        = $this->assetsPath . 'bg_certificate.jpg';
        $this->logoPath      = $this->assetsPath . 'logo_badge.png';
        $this->linePath      = $this->assetsPath . 'line_accent.png';
        $this->signaturePath = $this->assetsPath . 'signature_micha.png';
    }

    /**
     * Get resolved font directory path
     */
    protected function getFontPath(): string
    {
        $fontDirCandidates = [
            APPPATH . 'ThirdParty/fonts/',
            defined('FCPATH') ? FCPATH . 'assets/fonts/' : '',
            defined('ROOTPATH') ? ROOTPATH . 'public/assets/fonts/' : '',
            defined('ROOTPATH') ? ROOTPATH . 'assets/fonts/' : '',
            APPPATH . '../public/assets/fonts/',
        ];

        foreach ($fontDirCandidates as $dir) {
            if (!empty($dir) && is_dir($dir) && file_exists($dir . 'Georgia.json')) {
                return rtrim($dir, '/') . '/';
            }
        }

        return APPPATH . 'ThirdParty/fonts/';
    }

    /**
     * Generate PPTX as binary string in memory
     *
     * @param array $data Data peserta magang
     * @return string Binary content of PPTX
     */
    public function generatePptxString(array $data): string
    {
        $tempFile = sys_get_temp_dir() . '/pptx_cert_' . uniqid('', true) . '.pptx';
        $this->generatePptx($data, $tempFile);

        $binary = file_get_contents($tempFile);
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return $binary;
    }

    /**
     * Generate Single Certificate as PPTX file by replacing placeholders directly in template
     *
     * @param array $data Data peserta magang
     * @param string $destPath Destination file path
     * @return string Output PPTX file path
     */
    public function generatePptx(array $data, string $destPath): string
    {
        if (!file_exists($this->pptxTemplatePath)) {
            throw new \RuntimeException('File template PowerPoint tidak ditemukan di: ' . $this->pptxTemplatePath);
        }

        // Copy template to destination
        copy($this->pptxTemplatePath, $destPath);

        $namaLengkap       = !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : 'Peserta Magang';
        $periodeMulaiStr   = $this->formatIndonesianDate($data['periode_mulai'] ?? null, '13 April 2026');
        $periodeSelesaiStr = $this->formatIndonesianDate($data['periode_selesai'] ?? null, '13 Agustus 2026');
        $kota              = !empty($data['regional_interview']) ? $data['regional_interview'] : (!empty($data['kota_pilihan']) ? $data['kota_pilihan'] : 'Semarang');
        $tanggalTerbit     = $this->formatIndonesianDate(date('Y-m-d'), date('d F Y'));

        $replacements = [
            '[nama partisipan]'             => $namaLengkap,
            '[ nama partisipan ]'           => $namaLengkap,
            '[nama   partisipan]'           => $namaLengkap,
            '[periode mulai]'               => $periodeMulaiStr,
            '[ periode mulai ]'             => $periodeMulaiStr,
            '[periode   mulai]'             => $periodeMulaiStr,
            '[periode selesai]'             => $periodeSelesaiStr,
            '[ periode selesai ]'           => $periodeSelesaiStr,
            '[periode   selesai]'           => $periodeSelesaiStr,
            '[kota terbit sertifikat]'      => $kota,
            '[ kota terbit sertifikat ]'    => $kota,
            '[kota   terbit   sertifikat]'  => $kota,
            '[tanggal terbit sertifikat]'   => $tanggalTerbit,
            '[ tanggal terbit sertifikat ]' => $tanggalTerbit,
            '[tanggal   terbit   sertifikat]' => $tanggalTerbit,
        ];

        $zip = new ZipArchive();
        if ($zip->open($destPath) === true) {
            $slideXml = $zip->getFromName('ppt/slides/slide1.xml');
            if ($slideXml) {
                $dom = new DOMDocument();
                @$dom->loadXML($slideXml);
                $xpath = new DOMXPath($dom);
                $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
                $xpath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');

                $paragraphs = $xpath->query('//a:p');
                foreach ($paragraphs as $p) {
                    $runs = $xpath->query('./a:r', $p);
                    if ($runs->length === 0) continue;

                    // Read combined text of paragraph
                    $fullText = '';
                    foreach ($runs as $r) {
                        $tNodes = $xpath->query('./a:t', $r);
                        foreach ($tNodes as $t) {
                            $fullText .= $t->nodeValue;
                        }
                    }

                    if (strpos($fullText, '[') !== false) {
                        $replacedText = str_replace(array_keys($replacements), array_values($replacements), $fullText);

                        // Set first run's text
                        $firstRun = $runs->item(0);
                        $firstT = $xpath->query('./a:t', $firstRun)->item(0);
                        if ($firstT) {
                            $firstT->nodeValue = $replacedText;
                        } else {
                            $newT = $dom->createElementNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'a:t', $replacedText);
                            $firstRun->appendChild($newT);
                        }

                        // Remove subsequent runs in this paragraph to preserve original formatting
                        for ($i = $runs->length - 1; $i >= 1; $i--) {
                            $p->removeChild($runs->item($i));
                        }
                    }
                }

                $zip->addFromString('ppt/slides/slide1.xml', $dom->saveXML());
            }
            $zip->close();
        }

        return $destPath;
    }

    /**
     * Generate PDF directly as binary string in memory
     *
     * @param array $data Data peserta magang
     * @return string Binary content of PDF
     */
    public function generatePdfString(array $data): string
    {
        if (!class_exists('\\FPDF')) {
            if (file_exists(APPPATH . 'ThirdParty/fpdf/fpdf.php')) {
                require_once APPPATH . 'ThirdParty/fpdf/fpdf.php';
            } elseif (defined('ROOTPATH') && file_exists(ROOTPATH . 'vendor/setasign/fpdf/fpdf.php')) {
                require_once ROOTPATH . 'vendor/setasign/fpdf/fpdf.php';
            }
        }

        $fontPath = $this->getFontPath();
        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', $fontPath);
        }

        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        // Register custom typography
        $pdf->AddFont('Georgia', '', 'Georgia.json');
        $pdf->AddFont('Georgia', 'B', 'Georgia-Bold.json');
        $pdf->AddFont('RobotoSerif', '', 'RobotoSerif.json');
        $pdf->AddFont('RobotoSerif', 'B', 'RobotoSerif.json');

        $pdf->AddPage();

        // 1. Background Canvas (297 x 210 mm)
        if (file_exists($this->bgPath)) {
            $pdf->Image($this->bgPath, 0, 0, 297, 210);
        }

        // 2. Logo Badge (Top right: X=238.4, Y=6.4, W=49.3, H=17.5)
        if (file_exists($this->logoPath)) {
            $pdf->Image($this->logoPath, 238.4, 6.4, 49.3, 17.5);
        }

        // Color Scheme: Black, Text 1, Lighter 35% -> #565658 (RGB: 86, 86, 88)
        $pdf->SetTextColor(86, 86, 88);

        // 3. Title: SERTIFIKAT (Georgia 60 Regular)
        $pdf->SetFont('Georgia', '', 60);
        $pdf->SetXY(20.4, 32);
        $pdf->Cell(256.2, 22, 'SERTIFIKAT', 0, 0, 'C');

        // 4. Subtitle: diberikan kepada : (Roboto Serif 17)
        $pdf->SetFont('RobotoSerif', '', 17);
        $pdf->SetXY(20.4, 62);
        $pdf->Cell(256.2, 8, 'diberikan kepada :', 0, 0, 'C');

        // 5. Candidate Name: [nama partisipan] (Georgia 36 Regular)
        $namaLengkap = !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : 'Peserta Magang';
        
        $nameFontSize = 36;
        if (strlen($namaLengkap) > 35) {
            $nameFontSize = 26;
        } elseif (strlen($namaLengkap) > 28) {
            $nameFontSize = 30;
        }

        $pdf->SetFont('Georgia', '', $nameFontSize);
        $pdf->SetXY(20.4, 75);
        $pdf->Cell(256.2, 16, $namaLengkap, 0, 0, 'C');

        // 6. Accent Line (X=44, Y=96, W=208.7, H=2)
        if (file_exists($this->linePath)) {
            $pdf->Image($this->linePath, 44, 96, 208.7, 2);
        }

        // 7. Narrative text with dynamic period (Roboto Serif 17)
        $periodeMulaiStr   = $this->formatIndonesianDate($data['periode_mulai'] ?? null, '13 April 2026');
        $periodeSelesaiStr = $this->formatIndonesianDate($data['periode_selesai'] ?? null, '13 Agustus 2026');

        $pdf->SetFont('RobotoSerif', '', 17);
        $pdf->SetXY(20.4, 104);
        
        $narration = "Telah melaksanakan Program Industry-Academia Collaboration\n" .
                     "Program Di Indosat Ooredoo Hutchison Circle Java\n" .
                     "Terhitung mulai dari {$periodeMulaiStr} sampai {$periodeSelesaiStr}";
        $pdf->MultiCell(256.2, 7.5, $narration, 0, 'C');

        // 8. Place & Issue Date (Roboto Serif 17)
        $kota = !empty($data['regional_interview']) ? $data['regional_interview'] : (!empty($data['kota_pilihan']) ? $data['kota_pilihan'] : 'Semarang');
        $tanggalTerbit = $this->formatIndonesianDate(date('Y-m-d'), date('d F Y'));
        $placeAndDate = "{$kota}, {$tanggalTerbit}";

        $pdf->SetXY(20.4, 135);
        $pdf->Cell(256.2, 8, $placeAndDate, 0, 0, 'C');

        // 9. Digital Signature (Micha Heru)
        if (file_exists($this->signaturePath)) {
            $pdf->Image($this->signaturePath, 115, 143, 66.1, 27.1);
        }

        // 10. Micha Heru (Roboto Serif 17, Underlined)
        $pdf->SetFont('RobotoSerif', 'U', 17);
        $pdf->SetXY(20.4, 172);
        $pdf->Cell(256.2, 7, 'Micha Heru', 0, 0, 'C');

        // 11. VP - Head of Sales Effectiveness (Arial 13)
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetXY(20.4, 180);
        $pdf->Cell(256.2, 6, 'VP - Head of Sales Effectiveness', 0, 0, 'C');

        return $pdf->Output('S');
    }

    /**
     * Generate PDF and save to file
     *
     * @param array $data Data peserta magang
     * @param string $destPath Destination PDF path
     * @return string Output PDF file path
     */
    public function generatePdf(array $data, string $destPath): string
    {
        $binary = $this->generatePdfString($data);
        file_put_contents($destPath, $binary);
        return $destPath;
    }

    /**
     * Helper to format date into Indonesian (e.g. 13 April 2026)
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
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $day   = date('j', $time);
        $month = (int) date('n', $time);
        $year  = date('Y', $time);

        return "{$day} " . ($months[$month] ?? date('F', $time)) . " {$year}";
    }
}
