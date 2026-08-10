<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AddPendaftaranUniqueApplicantIndexes extends Migration
{
    public function up()
    {
        $table = 'pendaftaran_magang';

        // Do not silently discard applicants if an older production database
        // already contains duplicates. Resolve those rows first, then rerun
        // the migration.
        foreach (['email', 'nomor_whatsapp'] as $column) {
            $duplicate = $this->db->query(
                "SELECT {$column} FROM {$table} WHERE {$column} IS NOT NULL AND {$column} <> '' GROUP BY {$column} HAVING COUNT(*) > 1 LIMIT 1"
            )->getRowArray();

            if ($duplicate !== null) {
                throw new RuntimeException(
                    "Cannot add the unique {$column} index: existing duplicate value '{$duplicate[$column]}' was found in {$table}. "
                    . 'Resolve existing duplicates, then rerun this migration.'
                );
            }
        }

        if (!$this->hasUniqueIndexForColumn($table, 'email')) {
            $this->forge->addKey('email', false, true, 'uq_pendaftaran_magang_email');
        }

        if (!$this->hasUniqueIndexForColumn($table, 'nomor_whatsapp')) {
            $this->forge->addKey('nomor_whatsapp', false, true, 'uq_pendaftaran_magang_nomor_whatsapp');
        }

        $this->forge->processIndexes($table);
    }

    public function down()
    {
        // Do not remove these indexes during rollback: an older manual setup
        // may already own the email index, and removing either one would
        // reintroduce a production data-integrity issue.
    }

    private function hasUniqueIndexForColumn(string $table, string $column): bool
    {
        foreach ($this->db->query("SHOW INDEX FROM {$table}")->getResultArray() as $index) {
            if ($index['Column_name'] === $column && (int) $index['Non_unique'] === 0) {
                return true;
            }
        }

        return false;
    }
}
