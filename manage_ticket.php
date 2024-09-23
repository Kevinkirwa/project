<?php
session_start();
require 'db.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: login1.php");
    exit();
}

// Fetch tickets from the database
$stmt = $conn->prepare("SELECT tickets.id, tickets.title, tickets.description, tickets.status, tickets.created_at, users.username 
                         FROM tickets 
                         JOIN users ON tickets.user_id = users.id 
                         ORDER BY tickets.created_at DESC");
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update ticket status if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];

    $updateStmt = $conn->prepare("UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :ticket_id");
    $updateStmt->bindParam(':status', $status);
    $updateStmt->bindParam(':ticket_id', $ticket_id);
    $updateStmt->execute();

    header("Location: manage_ticket.php");
    exit();
}

// At the beginning, after starting the session
$user_id = $_SESSION['user_id']; // Make sure this is set

// Update the chat message handling block
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticket_id']) && isset($_POST['message'])) {
    $ticket_id = $_POST['ticket_id'];
    $chat_message = $_POST['message']; // Use 'message' instead of 'chat_message'

    $stmt = $conn->prepare("INSERT INTO chat (ticket_id, user_id, message) VALUES (:ticket_id, :user_id, :message)");
    $stmt->bindParam(':ticket_id', $ticket_id);
    $stmt->bindParam(':user_id', $user_id); // Bind the user ID
    $stmt->bindParam(':message', $chat_message);
    $stmt->execute();

    header("Location: manage_ticket.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Tickets</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h2>Manage Tickets</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Submitted By</th>
                <th>Submitted On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['username']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                            <select name="status" required>
                                <option value="Open" <?php if ($ticket['status'] == 'Open') echo 'selected'; ?>>Open</option>
                                <option value="In Progress" <?php if ($ticket['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                <option value="Resolved" <?php if ($ticket['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                            </select>
                            <button type="submit">Update Status</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <h4>Chat for Ticket ID: <?php echo htmlspecialchars($ticket['id']); ?></h4>
                        <div class="chat-box">
                            <?php
                            // Fetch chat messages for this ticket
                            $chatStmt = $conn->prepare("SELECT chat.message, chat.created_at, users.username 
                                                          FROM chat 
                                                          LEFT JOIN users ON chat.user_id = users.id 
                                                          WHERE chat.ticket_id = :ticket_id 
                                                          ORDER BY chat.created_at ASC");
                            $chatStmt->bindParam(':ticket_id', $ticket['id']);
                            $chatStmt->execute();
                            $messages = $chatStmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($messages as $msg): ?>
                                <p><strong><?php echo htmlspecialchars($msg['username'] ?? 'Admin'); ?>:</strong> 
                                   <?php echo htmlspecialchars($msg['message']); ?> <em><?php echo htmlspecialchars($msg['created_at']); ?></em></p>
                            <?php endforeach; ?>
                        </div>
                        <form method="POST" action="" style="display:flex;">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                            <input type="text" name="message" required placeholder="Type your message..." style="flex: 1;">
                            <button type="submit">Send</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
