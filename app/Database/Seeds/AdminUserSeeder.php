<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Reset table first to ensure clean state
        $this->db->table('admin_users')->truncate();

        $data = [
            [
                'username' => 'capabiltybuilding2025danseterusnya',
                'password' => password_hash('cbsantaibos123', PASSWORD_DEFAULT),
            ],
            [
                'username' => 'capability.center',
                'password' => password_hash('SkillUp@IOH', PASSWORD_DEFAULT),
            ],
            [
                'username' => 'internship@ioh',
                'password' => password_hash('Super@IOH123', PASSWORD_DEFAULT),
            ]
        ];

        $this->db->table('admin_users')->insertBatch($data);
    }
}
