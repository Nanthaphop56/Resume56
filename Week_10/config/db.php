<?php
// Simple procedural MySQL connection using mysqli
$host = 'localhost';
$user = 's68160385';
$pass = 'VGKZkK4B';
$dbname = 's68160385';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
