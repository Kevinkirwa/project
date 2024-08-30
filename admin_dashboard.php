<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.html');
    exit;
}

// Admin dashboard content goes here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="logout.php">Logout</a>
    <!-- Admin dashboard content here -->
</body>
</html>
