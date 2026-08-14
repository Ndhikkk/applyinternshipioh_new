<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PendaftaranModel;

/**
 * Jalankan manual:   php spark cleanup:pendaftaran
 * Dijadwalkan cron (Linux, jalan tiap hari jam 1 pagi), contoh crontab -e:
 *   0 1 * * * cd /path/ke/project && php spark cleanup:pendaftaran >> writable/logs/cleanup.log 2>&1
 *
 * Di Windows (Task Scheduler / Laragon), buat scheduled task yang menjalankan:
 *   php C:\path\ke\project\spark cleanup:pendaftaran
 *
 */
class Cleanuppendaftaran extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'cleanup:pendaftaran';
    protected $description = 'Arsipkan & hapus otomatis data pendaftaran magang sesuai kebijakan retensi (Arsip jika 2 minggu tidak ada perubahan pada updated_at -> dihapus/disembunyikan dari arsip jika 3 minggu tidak ada perubahan).';

    public function run(array $params)
    {
        $model = new PendaftaranModel();
        $twoWeeksAgo   = date('Y-m-d H:i:s', strtotime('-14 days'));
        $threeWeeksAgo = date('Y-m-d H:i:s', strtotime('-21 days'));

        // ---- TAHAP 1: masuk arsip jika tidak ada perubahan selama 2 minggu ----
        $inactive = $model->where('is_archived', 0)
            ->where('COALESCE(updated_at, created_at) <=', $twoWeeksAgo)
            ->findAll();
        $archivedCount = $this->archive($model, $inactive, 'Tidak ada perubahan (2 minggu)');

        // ---- TAHAP 2: hapus/sembunyikan dari arsip jika 3 minggu tidak ada perubahan ----
        $expired = $model->where('is_archived', 1)
            ->groupStart()
                ->where('COALESCE(updated_at, created_at) <=', $threeWeeksAgo)
                ->orWhere('archived_at <=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->groupEnd()
            ->findAll();
        $purgedCount = $this->purge($model, $expired, 'tidak ada perubahan 3 minggu / 7 hari di arsip');

        CLI::write("Selesai. {$archivedCount} data masuk arsip, {$purgedCount} data dihapus permanen.", 'green');
    }

    private function archive(PendaftaranModel $model, array $rows, string $reason): int
    {
        foreach ($rows as $row) {
            $model->update($row['id'], [
                'is_archived'     => 1,
                'archived_at'     => date('Y-m-d H:i:s'),
                'archived_reason' => $reason,
            ]);
            CLI::write("- Arsip #{$row['id']} {$row['nama_lengkap']} ({$reason})");
        }
        return count($rows);
    }

    private function purge(PendaftaranModel $model, array $rows, string $reason): int
    {
        foreach ($rows as $row) {
            $model->update($row['id'], [
                'is_archived'     => 1,
                'archived_at'     => date('Y-m-d H:i:s'),
                'archived_reason' => $reason,
            ]);
            CLI::write("- Arsipkan #{$row['id']} {$row['nama_lengkap']} ({$reason})");
        }

        return count($rows);
    }
}