<?php
$db = new mysqli('localhost', 'root', '', 'indosat_magang');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$result = $db->query("DESCRIBE pendaftaran_magang");
while($row = $result->fetch_assoc()) {
    echo $row["Field"] . "\n";
}
$db->close();
