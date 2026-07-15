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
class CleanupPendaftaran extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'cleanup:pendaftaran';
    protected $description = 'Arsipkan & hapus otomatis data pendaftaran magang sesuai kebijakan retensi (Ditolak 7 hari, Menunggu 6 bulan, Lolos/Diterima 1 tahun masuk arsip -> dihapus permanen 7 hari kemudian kalau tidak dipulihkan).';

    public function run(array $params)
    {
        $model = new PendaftaranModel();

        // ---- TAHAP 1: masuk arsip ----
        $rejected = $model->where('is_archived', 0)
            ->whereIn('status', ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'])
            ->where('status_changed_at IS NOT NULL')
            ->where('status_changed_at <=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->findAll();
        $archivedCount = $this->archive($model, $rejected, 'Ditolak (7 hari)');

        $waiting = $model->where('is_archived', 0)
            ->where('status', 'Menunggu')
            ->where('created_at <=', date('Y-m-d H:i:s', strtotime('-6 months')))
            ->findAll();
        $archivedCount += $this->archive($model, $waiting, 'Menunggu (6 bulan)');

        $accepted = $model->where('is_archived', 0)
            ->whereIn('status', ['Lolos_Final', 'Diterima'])
            ->where('status_changed_at IS NOT NULL')
            ->where('status_changed_at <=', date('Y-m-d H:i:s', strtotime('-1 year')))
            ->findAll();
        $archivedCount += $this->archive($model, $accepted, 'Lolos/Diterima (1 tahun)');

        // ---- TAHAP 2: sudah di arsip 7 hari -> hapus permanen ----
        $expired = $model->where('is_archived', 1)
            ->where('archived_at IS NOT NULL')
            ->where('archived_at <=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->findAll();
        $purgedCount = $this->purge($model, $expired, 'sudah 7 hari di arsip');

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
        $map = ['cv' => 'cv', 'surat_pengantar' => 'surat', 'ktm' => 'ktm'];

        foreach ($rows as $row) {
            foreach ($map as $field => $folder) {
                if (!empty($row[$field])) {
                    $path = WRITEPATH . 'uploads/' . $folder . '/' . $row[$field];
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
            }
            $model->delete($row['id']);
            CLI::write("- Hapus permanen #{$row['id']} {$row['nama_lengkap']} ({$reason})");
        }

        return count($rows);
    }
}