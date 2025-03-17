<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Fetch user's profile
$stmt = $pdo->prepare("SELECT profile_photo, description, batch_year FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$batch_year = $user['batch_year'];

// Handle new chat message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $insert_stmt = $pdo->prepare("INSERT INTO messages (sender_id, batch_year, message) VALUES (?, ?, ?)");
        $insert_stmt->execute([$user_id, $batch_year, $message]);
    }
}

// Fetch chat messages for the user's batch year
$message_stmt = $pdo->prepare("SELECT m.message, u.full_name, m.timestamp 
                               FROM messages m
                               JOIN users u ON m.sender_id = u.user_id
                               WHERE m.batch_year = ?
                               ORDER BY m.timestamp ASC");
$message_stmt->execute([$batch_year]);
$messages = $message_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unread notifications
$notifications_stmt = $pdo->prepare("SELECT message FROM notifications WHERE user_id = ? AND is_read = FALSE");
$notifications_stmt->execute([$user_id]);
$notifications = $notifications_stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark notifications as read
$mark_read_stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
$mark_read_stmt->execute([$user_id]);

$chat_stmt = $pdo->prepare("
    SELECT DISTINCT u.full_name, u.user_id, pc.message, pc.timestamp
    FROM personal_chats pc
    JOIN users u ON (pc.sender_id = u.user_id OR pc.receiver_id = u.user_id)
    WHERE (pc.sender_id = ? OR pc.receiver_id = ?)
    ORDER BY pc.timestamp DESC
    LIMIT 5
");
$chat_stmt->execute([$user_id, $user_id]);
$recent_chats = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>

        <div class="main-content">
            <!-- Notifications Section (Left Side) -->
            <div class="notifications">
                <h2>Notifications</h2>
                <?php if ($notifications): ?>
                    <ul>
                        <?php foreach ($notifications as $notification): ?>
                            <li><?php echo htmlspecialchars($notification['message']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No new notifications.</p>
                <?php endif; ?>
            </div>

            <!-- Profile Section (Right Side) -->
            <div class="profile">
                <img src="<?php echo htmlspecialchars($user['profile_photo'] ?? '/reunion/uploads/default-avatar.png'); ?>" alt="Profile Photo">
                <p><?php echo htmlspecialchars($user_name); ?></p>
                <p><?php echo htmlspecialchars($user['description'] ?? 'No description available.'); ?></p>
                <a href="profile.php">Edit Profile</a>
                <button onclick="location.href='index.html'" class="signin-btn">Home page</button>
            </div>
        </div>

        <!-- Chat Section -->
        <div class="chat-container">
            <h2>Batch Chat</h2>
            <!-- Messages Display -->
            <div class="chat-messages" id="chat-messages">
                <?php if ($messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <p>
                            <strong><?php echo htmlspecialchars($message['full_name']); ?>:</strong> 
                            <?php echo htmlspecialchars($message['message']); ?>
                            <span class="timestamp"><?php echo htmlspecialchars($message['timestamp']); ?></span>
                        </p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No messages yet.</p>
                <?php endif; ?>
            </div>

            <!-- Message Input -->
            <form method="POST" class="chat-form">
                <input type="text" name="message" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
        <div class="personal-chats">
        <div class="personal-chats">
    <h2>Recent Chats</h2>
    <ul>
        <?php foreach ($recent_chats as $chat): ?>
            <li>
                <a href="personal_chat.php?receiver_id=<?php echo $chat['user_id']; ?>">
                    <strong><?php echo htmlspecialchars($chat['full_name']); ?>:</strong>
                    <?php echo htmlspecialchars(substr($chat['message'], 0, 50)) . '...'; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


        <div class="center-actions">
            <p><a href="hero.php" class="find-friends-btn">Find your friends</a></p>
            <p><a href="logout.php" class="logout-btn">Logout</a></p>
        </div>
    </div>
</body>
</html>
