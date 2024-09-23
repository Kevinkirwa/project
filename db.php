<?php
$host = 'localhost';
$db = 'helpdesk_system';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "data";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>