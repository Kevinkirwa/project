<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .menu {
            display: none; /* Hide by default */
            position: absolute;
            top: 50px;
            left: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
        }
        .menu a:hover {
            background-color: #ddd;
        }
        .hamburger {
            cursor: pointer;
            font-size: 24px;
        }
        /* Show menu when active */
        .menu.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="hamburger" onclick="toggleMenu()">&#x22EE;</div> <!-- Three dots -->

    <div id="menu" class="menu">
        <h3>Admin Actions</h3>
        <a href="manage_ticket.php">Manage Tickets</a>
        <a href="logout.php">Logout</a>
    </div>

    <h2>Admin Dashboard</h2>

    <!-- Display message if any -->
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h3>Register a New User</h3>
    <form method="POST" action="">
        <label for="username">Username</label>
        <input type="text" name="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <label for="role">Role</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Register User</button>
    </form>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('active');
        }
    </script>
</body>
</html>
