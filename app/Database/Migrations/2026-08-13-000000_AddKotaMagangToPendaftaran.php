<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKotaMagangToPendaftaran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pendaftaran_magang', [
            'kota_pilihan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'regional_interview'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pendaftaran_magang', 'kota_pilihan');
    }
}
