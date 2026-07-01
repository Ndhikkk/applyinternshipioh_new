<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInterviewStages extends Migration
{
    public function up()
    {
        // Ubah kolom status untuk menambah tahapan interview
        $this->forge->modifyColumn('pendaftaran', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Menunggu', 'Diterima', 'Ditolak', 'Lolos_Interview_1', 'Tidak_Lolos_Interview_1', 'Lolos_Interview_2', 'Tidak_Lolos_Interview_2', 'Lolos_Interview_3', 'Tidak_Lolos_Interview_3', 'Lolos_Final'],
                'default' => 'Menunggu'
            ]
        ]);

        // Tambah kolom untuk catatan setiap tahap
        $this->forge->addColumn('pendaftaran', [
            'catatan_interview_1' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'catatan_admin'
            ],
            'catatan_interview_2' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'catatan_interview_1'
            ],
            'catatan_interview_3' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'catatan_interview_2'
            ],
            'jadwal_interview_1' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'catatan_interview_3'
            ],
            'jadwal_interview_2' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'jadwal_interview_1'
            ],
            'jadwal_interview_3' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'jadwal_interview_2'
            ]
        ]);
    }

    public function down()
    {
        // Kembalikan ke status semula
        $this->forge->modifyColumn('pendaftaran', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Menunggu', 'Diterima', 'Ditolak'],
                'default' => 'Menunggu'
            ]
        ]);

        // Hapus kolom tambahan
        $this->forge->dropColumn('pendaftaran', [
            'catatan_interview_1',
            'catatan_interview_2',
            'catatan_interview_3',
            'jadwal_interview_1',
            'jadwal_interview_2',
            'jadwal_interview_3'
        ]);
    }
}