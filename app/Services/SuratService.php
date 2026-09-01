<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use DOMXPath;

class SuratService
{
    protected string $suratPenerimaanTemplatePath;
    protected string $suratKeteranganSelesaiTemplatePath;

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
                            if (count($nodesInvolved) === 1) {
                                $firstNode->nodeValue = str_replace($search, $replaceVal, $firstNode->nodeValue);
                            } else {
                                $firstNode->nodeValue = $replaceVal;
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
}
