<?php
include 'db_connection.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: create_table.php');
            } else {
                header('Location: users.php');
            }
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        label { display: block; margin: 15px 0 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 5px 0; }
        input[type="submit"] { background-color: #5cb85c; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
        input[type="submit"]:hover { background-color: #4cae4c; }
        .signup-link { text-align: center; margin-top: 20px; }
        .message { margin-top: 20px; color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <p class="signup-link">Don't have an account? <a href="register.php">Sign Up</a></p>
        <p class="message"><?php echo $message; ?></p>
    </div>
</body>
</html>
