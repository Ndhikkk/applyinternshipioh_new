<?php
$db = new mysqli('localhost', 'root', '', 'indosat_magang');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$result = $db->query("SELECT id, email, nomor_whatsapp, is_archived FROM pendaftaran_magang");
echo "Total records in DB: " . $result->num_rows . "\n";
while($row = $result->fetch_assoc()) {
    echo "Found: ID=" . $row["id"]. " - Email=" . $row["email"]. " - WA=" . $row["nomor_whatsapp"] . " - Archived=" . $row["is_archived"] . "\n";
}
$db->close();
