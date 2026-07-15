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
        'email',
        'nomor_whatsapp',
        'asal_kampus',
        'program_studi',
        'divisi_pilihan',
        'semester',
        'jenis_magang',
        'periode_mulai',
        'periode_selesai',
        'cv',
        'surat_pengantar',
        'ktm',
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

    /**
     * Semua status yang valid, dipakai untuk validasi & tampilan badge.
     */
    public static function statusList(): array
    {
        return [
            'Menunggu',
            'Lolos_Interview_1', 'Tidak_Lolos_Interview_1',
            'Lolos_Interview_2', 'Tidak_Lolos_Interview_2',
            'Lolos_Interview_3', 'Tidak_Lolos_Interview_3',
            'Lolos_Final',
            'Diterima',
            'Ditolak',
        ];
    }
}