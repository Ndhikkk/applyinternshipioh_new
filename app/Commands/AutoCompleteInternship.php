<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PendaftaranModel;

/**
 * Jalankan manual: php spark internship:complete
 * Cron harian: 0 1 * * * cd /path/ke/project && php spark internship:complete >> writable/logs/autocomplete.log 2>&1
 * Alternatif: cukup pakai `php spark cleanup:pendaftaran` yang sudah include auto-complete.
 */
class AutoCompleteInternship extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'internship:complete';
    protected $description = 'Otomatis ubah status Diterima -> Complete jika periode_selesai sudah lewat (berdasarkan tanggal hari ini Asia/Jakarta).';

    public function run(array $params)
    {
        $model = new PendaftaranModel();

        // Optional: allow custom date for testing -> php spark internship:complete 2026-08-30
        $today = $params[0] ?? null;
        if ($today !== null) {
            // validasi Y-m-d
            $d = \DateTime::createFromFormat('Y-m-d', $today);
            if (!$d || $d->format('Y-m-d') !== $today) {
                CLI::error("Tanggal tidak valid: {$today}. Gunakan format Y-m-d (contoh: 2026-08-30).");
                return;
            }
        }

        $affected = $model->autoCompleteExpired($today);
        if ($affected > 0) {
            CLI::write("Sukses: {$affected} kandidat Diterima -> Complete (periode_selesai <= " . ($today ?? date('Y-m-d')) . ").", 'green');
        } else {
            CLI::write("Tidak ada kandidat yang perlu di-Complete untuk tanggal " . ($today ?? date('Y-m-d')) . ".", 'yellow');
        }
    }
}
