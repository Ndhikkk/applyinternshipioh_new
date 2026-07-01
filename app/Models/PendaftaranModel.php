<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table = 'pendaftaran_magang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama_lengkap',
        'email',
        'nomor_whatsapp',
        'asal_kampus',
        'program_studi',
        'semester',
        'jenis_magang',
        'periode_mulai',
        'periode_selesai',
        'cv',
        'surat_pengantar',
        'ktm',
        'status',
        'catatan_admin',
        'catatan_interview_1',
        'catatan_interview_2',
        'catatan_interview_3',
        'jadwal_interview_1',
        'jadwal_interview_2',
        'jadwal_interview_3'
    ];
    protected $useTimestamps = true;

    // Method untuk mendapatkan status berikutnya
    public function getNextStatus($currentStatus)
    {
        $statusFlow = [
            'Menunggu' => 'Lolos_Interview_1',
            'Lolos_Interview_1' => 'Lolos_Interview_2',
            'Lolos_Interview_2' => 'Lolos_Interview_3',
            'Lolos_Interview_3' => 'Lolos_Final'
        ];

        return $statusFlow[$currentStatus] ?? null;
    }

    public function getRejectStatus($currentStatus)
    {
        $rejectStatus = [
            'Menunggu' => 'Ditolak',
            'Lolos_Interview_1' => 'Tidak_Lolos_Interview_1',
            'Lolos_Interview_2' => 'Tidak_Lolos_Interview_2',
            'Lolos_Interview_3' => 'Tidak_Lolos_Interview_3'
        ];

        return $rejectStatus[$currentStatus] ?? 'Ditolak';
    }
}