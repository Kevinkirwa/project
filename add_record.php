<?php
include 'db_connection.php';  // Include the database configuration file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableName = $_POST['table_name'];
    $columns = $_POST['columns'];
    $values = $_POST['values'];

    $columnsStr = implode(', ', $columns);
    $valuesStr = "'" . implode("', '", $values) . "'";

    $sql = "INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)";

    if (mysqli_query($conn, $sql)) {
        echo "Record added successfully.";
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}
?>

<form method="POST">
    <input type="text" name="table_name" placeholder="Table Name" required>
    <input type="text" name="columns[]" placeholder="Column Name" required>
    <input type="text" name="values[]" placeholder="Value" required>
    <!-- Add more columns and values as needed -->
    <input type="submit" value="Add Record">
</form>
