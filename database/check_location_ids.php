<?php
require_once __DIR__ . '/../config/database.php';
$res = mysqli_query($mysqli, "SELECT id, no_rak, status FROM location ORDER BY id ASC LIMIT 10");
while ($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | Rak: " . $row['no_rak'] . "\n";
}
?>
