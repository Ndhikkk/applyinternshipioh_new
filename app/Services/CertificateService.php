<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use DOMXPath;

class CertificateService
{
    protected string $pptxTemplatePath;

    public function __construct()
    {
        $this->pptxTemplatePath = WRITEPATH . 'templates/Sertifikat Selesai Industry-Academia Collaboration Program.pptx';
    }

    /**
     * Generate Single Certificate as PPTX by replacing placeholders directly in the template
     *
     * @param array $data Data peserta magang
     * @param string|null $destPath Destination file path
     * @return string Output PPTX file path
     */
    public function generatePptx(array $data, ?string $destPath = null): string
    {
        if (!file_exists($this->pptxTemplatePath)) {
            throw new \RuntimeException('File template PowerPoint tidak ditemukan di: ' . $this->pptxTemplatePath);
        }

        if (empty($destPath)) {
            $namaLengkap = !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : 'Peserta';
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $namaLengkap);
            $destPath = WRITEPATH . 'temp_cert_' . time() . '_' . $cleanName . '.pptx';
        }

        // Copy template to destination
        copy($this->pptxTemplatePath, $destPath);

        $namaLengkap       = !empty($data['nama_lengkap']) ? trim($data['nama_lengkap']) : 'Peserta Magang';
        $periodeMulaiStr   = $this->formatIndonesianDate($data['periode_mulai'] ?? null, '13 April 2026');
        $periodeSelesaiStr = $this->formatIndonesianDate($data['periode_selesai'] ?? null, '13 Agustus 2026');
        $kota              = !empty($data['regional_interview']) ? $data['regional_interview'] : (!empty($data['kota_pilihan']) ? $data['kota_pilihan'] : 'Semarang');
        $tanggalTerbit     = $this->formatIndonesianDate($data['periode_selesai'] ?? date('Y-m-d'), date('d F Y'));

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
