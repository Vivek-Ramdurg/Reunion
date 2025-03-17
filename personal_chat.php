<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? null;

if (!$receiver_id) {
    die('Invalid receiver.');
}

// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch receiver details
$stmt = $pdo->prepare("SELECT full_name, profile_photo FROM users WHERE user_id = ?");
$stmt->execute([$receiver_id]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receiver) {
    die('User not found.');
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO personal_chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
    }
}

// Fetch chat messages between sender and receiver
$stmt = $pdo->prepare("
    SELECT pc.message, pc.timestamp, u.full_name 
    FROM personal_chats pc
    JOIN users u ON pc.sender_id = u.user_id
    WHERE (pc.sender_id = ? AND pc.receiver_id = ?) OR (pc.sender_id = ? AND pc.receiver_id = ?)
    ORDER BY pc.timestamp ASC
");
$stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Chat</title>
    <link rel="stylesheet" href="chat.css">
</head>
<style>
    
</style>
<body>
    <div class="chat-container">
        <h1>Chat with <?php echo htmlspecialchars($receiver['full_name']); ?></h1>
        
        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $msg): ?>
                <p>
                    <strong><?php echo htmlspecialchars($msg['full_name']); ?>:</strong> 
                    <?php echo htmlspecialchars($msg['message']); ?>
                    <span class="timestamp"><?php echo htmlspecialchars($msg['timestamp']); ?></span>
                </p>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" class="chat-form">
            <input type="text" name="message" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
        <button onclick="location.href='dashboard.php'" class="signin-btn">dashboard</button>
    </div>
</body>
</html>
