<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKotaPilihanToPendaftaran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pendaftaran_magang', [
            'kota_pilihan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'program_studi',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pendaftaran_magang', 'kota_pilihan');
    }
}
