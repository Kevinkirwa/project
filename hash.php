<?php
$password = '123456'; // Replace with your plain text password

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo "Hashed Password: " . $hashedPassword;
?>
