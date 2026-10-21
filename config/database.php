<?php
// Update these values to match your local XAMPP/MySQL setup.
$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = ''; // If your MySQL root account has a password, enter it here.
$dbName = 'sunson_solar';

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
if ($conn->connect_error) {
    error_log('Database connection error: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database connection failed. Check config/database.php and make sure MySQL is running.');
}
$conn->set_charset('utf8mb4');
