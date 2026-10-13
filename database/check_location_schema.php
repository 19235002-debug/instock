<?php
require_once __DIR__ . '/../config/database.php';
$res = mysqli_query($mysqli, "DESCRIBE location");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " | " . $row['Type'] . " | Null:" . $row['Null'] . " | Key:" . $row['Key'] . " | Extra:" . $row['Extra'] . "\n";
}
?>
