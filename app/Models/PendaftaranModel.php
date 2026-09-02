<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table            = 'pendaftaran_magang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $dateFormat       = 'datetime';

    // NOTE: tambahkan / sesuaikan field ini kalau kolom Anda beda nama.
    // Field-field jadwal_interview_*, link_zoom_*, catatan_interview_*,
    // catatan_admin, email_terkirim BARU ada setelah menjalankan
    // 01_MIGRASI_DATABASE.sql
    protected $allowedFields = [
        'token_pendaftaran',
        'nama_lengkap',
        'nim',
        'email',
        'nomor_whatsapp',
        'asal_kampus',
        'program_studi',
        'regional_interview',
        'kota_pilihan',
        'divisi_pilihan',
        'semester',
        'jenis_magang',
        'periode_mulai',
        'periode_selesai',
        'cv',
        'surat_pengantar',
        'proposal_magang',
        'ktm',
        'nomor_darurat',
        'status',
        'import_source',
        'referral_token',
        'batch_id',
        'catatan',
        'jadwal_interview_1',
        'jadwal_interview_2',
        'jadwal_interview_3',
        'link_zoom_1',
        'link_zoom_2',
        'link_zoom_3',
        'catatan_interview_1',
        'catatan_interview_2',
        'catatan_interview_3',
        'catatan_admin',
        'email_terkirim',
        'status_changed_at',
        'is_archived',
        'archived_at',
        'archived_reason',
    ];

    protected $beforeInsert = ['cleanDateFields'];
    protected $beforeUpdate = ['cleanDateFields'];

    /**
     * Otomatis ubah string kosong '' atau '0000-00-00' pada kolom bertipe DATE/DATETIME
     * menjadi NULL sebelum disimpan ke database agar tidak crash dengan error
     * "mysqli_sql_exception: Incorrect DATE value: ''".
     */
    protected function cleanDateFields(array $data): array
    {
        $dateFields = [
            'periode_mulai',
            'periode_selesai',
            'jadwal_interview_1',
            'jadwal_interview_2',
            'jadwal_interview_3',
            'archived_at',
            'status_changed_at'
        ];

        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($dateFields as $field) {
                if (array_key_exists($field, $data['data'])) {
                    $val = $data['data'][$field];
                    if ($val === null || trim((string)$val) === '' || $val === '0000-00-00') {
                        $data['data'][$field] = null;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Semua status yang valid, dipakai untuk validasi & tampilan badge.
     */
    public static function statusList(): array
    {
        return [
            'Menunggu',
            'Progress',
            'Diterima',
            'Complete',
            'Ditolak'
        ];
    }

    /**
     * Otomatis ubah status Diterima -> Complete jika periode_selesai sudah lewat.
     * Dipanggil di Admin::dashboard, Progres::cek, Filter global, dan cron.
     *
     * Logic: status = 'Diterima' AND periode_selesai IS NOT NULL
     *        AND periode_selesai > '1000-01-01' (menghindari error DATE '' di MySQL strict mode)
     *        AND periode_selesai <= $today  => set jadi 'Complete'
     *
     * @param string|null $today Override tanggal Y-m-d (default hari ini Asia/Jakarta)
     * @return int Jumlah row yang diupdate
     */
    public function autoCompleteExpired(?string $today = null): int
    {
        if ($today === null) {
            // pakai timezone app (Asia/Jakarta) supaya selaras dengan config App.php
            $tz = function_exists('app_timezone') ? (app_timezone() ?: 'Asia/Jakarta') : 'Asia/Jakarta';
            $today = (new \DateTime('now', new \DateTimeZone($tz)))->format('Y-m-d');
        }

        $now = date('Y-m-d H:i:s');

        // Bulk UPDATE via Query Builder langsung ke table - aman untuk MySQL strict mode
        $builder = $this->db->table($this->table);
        $builder->where('status', 'Diterima')
                ->where('periode_selesai IS NOT NULL', null, false)
                ->where("CAST(periode_selesai AS CHAR) !=", '')
                ->where("CAST(periode_selesai AS CHAR) !=", '0000-00-00')
                ->where('periode_selesai >', '1000-01-01')
                ->where('periode_selesai <=', $today)
                ->set([
                    'status'            => 'Complete',
                    'status_changed_at' => $now,
                    'updated_at'        => $now,
                ]);

        $builder->update();

        return (int) $this->db->affectedRows();
    }

    /**
     * Helper: apakah kandidat sudah lewat periode_selesai dan harus Complete?
     */
    public static function shouldBeComplete(array $row, ?string $today = null): bool
    {
        if (($row['status'] ?? null) !== 'Diterima') {
            return false;
        }
        $selesai = $row['periode_selesai'] ?? null;
        if (empty($selesai) || $selesai === '0000-00-00' || $selesai <= '1000-01-01') {
            return false;
        }
        if ($today === null) {
            $tz = function_exists('app_timezone') ? (app_timezone() ?: 'Asia/Jakarta') : 'Asia/Jakarta';
            $today = (new \DateTime('now', new \DateTimeZone($tz)))->format('Y-m-d');
        }
        // bandingkan sebagai DATE string Y-m-d (lexicographically comparable)
        return $selesai <= $today;
    }
}
