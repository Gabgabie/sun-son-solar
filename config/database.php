<?php
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'sunson_solar';
$port = 3307; // Change this to the MySQL/MariaDB port shown in XAMPP.

mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    error_log('Sun Son Solar DB connection failed: ' . $conn->connect_error);
    http_response_code(500);
    die('Database connection failed. Check config/database.php and confirm the database was imported.');
}

$conn->set_charset('utf8mb4');
