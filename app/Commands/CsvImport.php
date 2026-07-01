<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PendaftaranModel;
use App\Models\AdminModel;

class CsvImport extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'csv:import';
    protected $description = 'Import data from CSV files in database/csv/';

    public function run(array $params)
    {
        helper('filesystem');

        // Import pendaftaran_magang.csv
        $pendaftaranFile = ROOTPATH . 'database/csv/pendaftaran_magang.csv';
        if (is_file($pendaftaranFile)) {
            $pendaftaranModel = new PendaftaranModel();

            $handle = fopen($pendaftaranFile, "r");
            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row = [
                    'nama_lengkap' => $data[0],
                    'nomor_whatsapp' => $data[1],
                    'asal_kampus' => $data[2],
                    'program_studi' => $data[3],
                    'semester' => $data[4],
                    'status' => $data[5],
                    'catatan' => $data[6],
                    // Set dummy paths for required files
                    'cv' => 'uploads/dummy_cv.pdf',
                    'surat_pengantar' => 'uploads/dummy_surat.pdf',
                    'ktm' => 'uploads/dummy_ktm.jpg'
                ];
                $pendaftaranModel->insert($row);
            }
            fclose($handle);
            CLI::write('Imported pendaftaran_magang.csv', 'green');
        }

        // Import admin_users.csv
        $adminFile = ROOTPATH . 'database/csv/admin_users.csv';
        if (is_file($adminFile)) {
            $adminModel = new AdminModel();

            $handle = fopen($adminFile, "r");
            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row = [
                    'username' => $data[0],
                    'password' => $data[1] // Password should be already hashed in CSV
                ];
                $adminModel->insert($row);
            }
            fclose($handle);
            CLI::write('Imported admin_users.csv', 'green');
        }

        CLI::write('CSV import completed', 'green');
    }
}