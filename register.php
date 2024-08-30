<?php
include 'db_connection.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $role = mysqli_real_escape_string($conn, $_POST['role']);  // 'user' or 'admin'

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

    if (mysqli_query($conn, $sql)) {
        $message = "Registration successful. You can now <a href='login.php'>login</a>.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        label { display: block; margin: 15px 0 5px; }
        input[type="text"], input[type="password"], select { width: 100%; padding: 10px; margin: 5px 0; }
        input[type="submit"] { background-color: #5cb85c; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
        input[type="submit"]:hover { background-color: #4cae4c; }
        .message { margin-top: 20px; color: green; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <input type="submit" value="Sign Up">
        </form>

        <p class="message"><?php echo $message; ?></p>
    </div>
</body>
</html>
