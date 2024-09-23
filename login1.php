<?php
session_start();
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // This should match the value in the database

        // Debugging output
        echo "User Role: " . htmlspecialchars($user['role']) . "<br>";
        echo "Role: " . $_SESSION['role'];


        // Redirect based on role
        if (trim($user['role']) === 'admin') { // Check for admin role
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: submit_ticket.php");
            exit();
        }
    } else {
        echo "Invalid credentials!"; // Invalid login message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <label for="username">Username</label>
        <input type="text" name="username" required>
        
        <label for="password">Password</label>
        <input type="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
