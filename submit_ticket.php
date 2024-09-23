<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle ticket submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $status = 'Open';

    try {
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, title, description, category, priority, status, created_at) VALUES (:user_id, :title, :description, :category, :priority, :status, NOW())");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $message = 'Ticket submitted successfully!';
        header("Location: submit_ticket.php"); // Redirect back to the same page to show the chat
        exit();
    } catch (PDOException $e) {
        $message = 'Error submitting ticket: ' . $e->getMessage();
    }
}

// Handle chat message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['chat_message']) && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $chat_message = $_POST['chat_message'];

    $stmt = $conn->prepare("INSERT INTO chat (ticket_id, user_id, message) VALUES (:ticket_id, :user_id, :message)");
    $stmt->bindParam(':ticket_id', $ticket_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':message', $chat_message);
    $stmt->execute();
}

// Fetch user tickets
$ticketsStmt = $conn->prepare("SELECT * FROM tickets WHERE user_id = :user_id");
$ticketsStmt->bindParam(':user_id', $user_id);
$ticketsStmt->execute();
$tickets = $ticketsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Ticket</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h2>Submit a New Ticket</h2>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="title">Title</label>
        <input type="text" name="title" required>

        <label for="description">Description</label>
        <textarea name="description" required></textarea>

        <label for="category">Category</label>
        <select name="category" required>
            <option value="Networking">Networking</option>
            <option value="Hardware">Hardware</option>
            <option value="Software">Software</option>
        </select>

        <label for="priority">Priority</label>
        <select name="priority" required>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>

        <button type="submit">Submit Ticket</button>
    </form>

    <h2>Your Tickets and Chat</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Chat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                    <td>
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
                            $chatMessages = $chatStmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($chatMessages as $msg):
                            ?>
                                <p><strong><?php echo htmlspecialchars($msg['username'] ?? 'You'); ?>:</strong> 
                                   <?php echo htmlspecialchars($msg['message']); ?> <em><?php echo htmlspecialchars($msg['created_at']); ?></em></p>
                            <?php endforeach; ?>
                        </div>
                        <form method="POST" action="" style="display:flex;">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                            <input type="text" name="chat_message" required placeholder="Type your message..." style="flex: 1;">
                            <button type="submit">Send</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
