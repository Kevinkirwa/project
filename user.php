<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Fetch all table names from the database
$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error retrieving tables: " . mysqli_error($conn);
    exit;
}

echo '<nav>
        <button onclick="toggleMenu()">☰ Menu</button>
        <div id="menu" style="display:none;">';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $tableName = $row[0];
        echo "<a href='?table=$tableName'>$tableName</a><br>";
    }
}

echo '</div></nav>';

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];
    echo "<h3>Contents of Table '$tableName'</h3>";

    $sqlTableData = "SELECT * FROM $tableName";
    $resultTableData = mysqli_query($conn, $sqlTableData);

    if (!$resultTableData) {
        echo "Error retrieving data from table '$tableName': " . mysqli_error($conn);
    } elseif (mysqli_num_rows($resultTableData) > 0) {
        echo "<table border='1'><tr>";
        $fields = mysqli_fetch_fields($resultTableData);
        foreach ($fields as $field) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";
        while ($rowData = mysqli_fetch_assoc($resultTableData)) {
            echo "<tr>";
            foreach ($rowData as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found in the table '$tableName'.";
    }
}

mysqli_close($conn);
?>

<script>
function toggleMenu() {
    const menu = document.getElementById('menu');
    if (menu.style.display === 'none') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}
</script>
