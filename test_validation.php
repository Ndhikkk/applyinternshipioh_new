<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://localhost:8080/index.php/pendaftaran/store");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'nama_lengkap' => 'Jeki',
    'email' => 'jeki@gmail.com',
    'nomor_whatsapp' => '081234567895',
    'nomor_darurat' => '081234567896',
    'asal_kampus' => 'UI',
    'program_studi' => 'TI',
    'divisi_pilihan' => 'HR',
    'semester' => 5,
    'ipk' => '3.5',
    'domisili' => 'Jakarta'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close($ch);
echo $server_output;
