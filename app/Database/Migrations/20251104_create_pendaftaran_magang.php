<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToPendaftaranMagang extends Migration
{
    public function up()
    {
        // Tambah kolom yang diperlukan
        $this->forge->addColumn('pendaftaran', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'nama_lengkap'
            ],
            'jenis_magang' => [
                'type' => 'ENUM',
                'constraint' => ['Wajib', 'Mandiri'],
                'default' => 'Wajib',
                'after' => 'semester'
            ],
            'periode_mulai' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'jenis_magang'
            ],
            'periode_selesai' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'periode_mulai'
            ],
            'kode_pendaftaran' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id'
            ]
        ]);

        // Ubah nama kolom catatan menjadi catatan_admin untuk konsistensi
        $this->forge->modifyColumn('pendaftaran', [
            'catatan' => [
                'name' => 'catatan_admin',
                'type' => 'TEXT',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan
        $this->forge->dropColumn('pendaftaran', [
            'email',
            'jenis_magang',
            'periode_mulai',
            'periode_selesai',
            'kode_pendaftaran'
        ]);

        // Kembalikan nama kolom catatan
        $this->forge->modifyColumn('pendaftaran', [
            'catatan_admin' => [
                'name' => 'catatan',
                'type' => 'TEXT',
                'null' => true
            ]
        ]);
    }
}