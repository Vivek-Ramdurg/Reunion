<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
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
    die("Error: " . $e->getMessage());
}

// Get current user and batch year
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT batch_year FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$batch_year = $user['batch_year'];

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, batch_year, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $batch_year, $message]);
    }
}

if (!isset($_SESSION['displayed_messages'])) {
    $_SESSION['displayed_messages'] = [];
}

// Fetch messages for the user's batch year
$stmt = $pdo->prepare("SELECT m.message, u.full_name, m.timestamp 
                       FROM messages m
                       JOIN users u ON m.sender_id = u.user_id
                       WHERE m.batch_year = ?
                       ORDER BY m.timestamp ASC");
$stmt->execute([$batch_year]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
