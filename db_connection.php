<?php
// XAMPP default database connection settings
$host = 'localhost';         // Hostname for XAMPP MySQL server
$dbname = 'project';         // Replace with your database name
$username = 'root';          // Default XAMPP MySQL username
$password = '';              // Default XAMPP MySQL password (usually empty)

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Optional: Set character set to UTF-8
mysqli_set_charset($conn, 'utf8');

// Success message
echo 'Connection successful!';
?>
