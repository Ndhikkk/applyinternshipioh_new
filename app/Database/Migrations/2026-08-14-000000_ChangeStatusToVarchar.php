<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeStatusToVarchar extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('pendaftaran_magang', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Menunggu',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Revert if needed
    }
}
