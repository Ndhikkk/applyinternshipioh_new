<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameKotaMagangToKotaPilihan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // If kota_magang exists and kota_pilihan doesn't, rename column
        if ($db->fieldExists('kota_magang', 'pendaftaran_magang') && !$db->fieldExists('kota_pilihan', 'pendaftaran_magang')) {
            $this->forge->modifyColumn('pendaftaran_magang', [
                'kota_magang' => [
                    'name'       => 'kota_pilihan',
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
            ]);
        } elseif (!$db->fieldExists('kota_pilihan', 'pendaftaran_magang')) {
            $this->forge->addColumn('pendaftaran_magang', [
                'kota_pilihan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'regional_interview'],
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('kota_pilihan', 'pendaftaran_magang') && !$db->fieldExists('kota_magang', 'pendaftaran_magang')) {
            $this->forge->modifyColumn('pendaftaran_magang', [
                'kota_pilihan' => [
                    'name'       => 'kota_magang',
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
            ]);
        }
    }
}
