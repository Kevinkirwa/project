<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.html');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $tableName = $_POST['table_name'];
        $columns = $_POST['columns'];
        $columnsArray = explode(',', $columns);

        $columnDefinitions = [];
        foreach ($columnsArray as $column) {
            $columnDefinitions[] = "$column VARCHAR(255)";
        }
        $columnsSql = implode(', ', $columnDefinitions);

        $sql = "CREATE TABLE $tableName ($columnsSql)";
        if (mysqli_query($conn, $sql)) {
            $message = "Table '$tableName' created successfully.";
        } else {
            $message = "Error creating table: " . mysqli_error($conn);
        }
    } elseif ($action === 'add_column') {
        $tableName = $_POST['table_name'];
        $newColumn = $_POST['new_column'];

        $sql = "ALTER TABLE $tableName ADD COLUMN $newColumn VARCHAR(255)";
        if (mysqli_query($conn, $sql)) {
            $message = "Column '$newColumn' added to table '$tableName'.";
        } else {
            $message = "Error adding column: " . mysqli_error($conn);
        }
    } elseif ($action === 'add_record') {
        $tableName = $_POST['table_name'];
        $columns = $_POST['columns'];
        $values = $_POST['values'];

        $columnsStr = implode(', ', $columns);
        $valuesStr = "'" . implode("', '", $values) . "'";

        $sql = "INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)";
        if (mysqli_query($conn, $sql)) {
            $message = "Record added successfully.";
        } else {
            $message = "Error adding record: " . mysqli_error($conn);
        }
    }
}

$tableOptions = '';
$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $tableOptions .= "<option value='{$row[0]}'>{$row[0]}</option>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script>
        function updateTables() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('tables').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        setInterval(updateTables, 5000);

        function handleFormSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('message').innerText = this.responseText;
                    updateTables();
                }
            };
            xhr.send(formData);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('createForm').addEventListener('submit', handleFormSubmit);
            document.getElementById('addColumnForm').addEventListener('submit', handleFormSubmit);
            document.getElementById('addRecordForm').addEventListener('submit', handleFormSubmit);
        });
    </script>
</head>
<body>
    <h2>Create a New Table</h2>
    <form id="createForm" method="POST">
        <input type="hidden" name="action" value="create">
        <label for="table_name">Table Name:</label>
        <input type="text" id="table_name" name="table_name" required><br><br>
        <label for="columns">Columns (comma separated):</label>
        <input type="text" id="columns" name="columns" required><br><br>
        <input type="submit" value="Create Table">
    </form>

    <h2>Add Column to Existing Table</h2>
    <form id="addColumnForm" method="POST">
        <input type="hidden" name="action" value="add_column">
        <label for="table_name">Table Name:</label>
        <select id="table_name" name="table_name">
            <?php echo $tableOptions; ?>
        </select><br><br>
        <label for="new_column">New Column Name:</label>
        <input type="text" id="new_column" name="new_column" required><br><br>
        <input type="submit" value="Add Column">
    </form>

    <h2>Add Record to Table</h2>
    <form id="addRecordForm" method="POST">
        <input type="hidden" name="action" value="add_record">
        <label for="table_name">Table Name:</label>
        <select id="table_name" name="table_name">
            <?php echo $tableOptions; ?>
        </select><br><br>
        <label for="columns[]">Column Name:</label>
        <input type="text" id="columns" name="columns[]" required><br><br>
        <label for="values[]">Value:</label>
        <input type="text" id="values" name="values[]" required><br><br>
        <input type="submit" value="Add Record">
    </form>

    <h2>Current Tables and Contents</h2>
    <div id="tables">
        <!-- Tables and contents will be loaded here -->
    </div>

    <div id="message"><?php echo $message; ?></div>
</body>
</html>
