<?php
// Database connection for the local XAMPP setup.
$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "sunson_solar";
$port = 3307; // Use the port shown in your MariaDB terminal.

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    error_log("Database connection error: " . $conn->connect_error);
    die("Database connection failed. Check the settings in config/database.php.");
}

$conn->set_charset("utf8mb4");
