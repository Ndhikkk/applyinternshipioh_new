<?php
require 'vendor/autoload.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
$db = \Config\Database::connect();
try {
    $builder = $db->table('pendaftaran_magang')
        ->select('1')
        ->where('email', 'jeki@gmail.com')
        ->limit(1);
    $result = $builder->get()->getRow();
    echo "Query builder SUCCESS. Result: " . print_r($result, true) . "\n";
} catch (\Exception $e) {
    echo "Query builder FAILED: " . $e->getMessage() . "\n";
}
