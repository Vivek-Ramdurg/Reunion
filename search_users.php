<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Unauthorized access.');
}

$user_id = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get the logged-in user's district and college
$stmt = $pdo->prepare("SELECT district_id, institution_id FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userInfo) {
    die('User not found.');
}

$batchYear = $_GET['batch_year'] ?? '';

if ($batchYear) {
    $stmt = $pdo->prepare("
        SELECT user_id, full_name, email, profile_photo, description 
        FROM users 
        WHERE district_id = ? AND institution_id = ? AND batch_year = ?
    ");
    $stmt->execute([$userInfo['district_id'], $userInfo['institution_id'], $batchYear]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        $counter = 1; // Counter for numbering the users
        foreach ($users as $user) {
            // Display user's information in a small box
            echo '<div class="user-box">';
            
            // Display the user number
            echo '<div class="user-number">' . $counter++ . '</div>';

            // Display profile photo
            if ($user['profile_photo']) {
                echo '<img src="' . htmlspecialchars($user['profile_photo']) . '" alt="Profile Photo" class="profile-photo">';
            } else {
                echo '<img src="default-profile.png" alt="Profile Photo" class="profile-photo">';
            }
            
            // Display name, email, and description
            echo '<div class="user-details">';
            echo '<p><strong>' . htmlspecialchars($user['full_name']) . '</strong></p>';
            echo '<p>Email: ' . htmlspecialchars($user['email']) . '</p>';
            echo '<p>Description: ' . htmlspecialchars($user['description'] ?? 'No description available.') . '</p>';
            echo '</div>';

            // Add chat button with a link to personal_chat.php
            echo '<a href="personal_chat.php?receiver_id=' . htmlspecialchars($user['user_id']) . '" class="chat-btn">Chat</a>';

            echo '</div>';
        }
    } else {
        echo '<p>No users found for the selected batch.</p>';
    }
} else {
    echo '<p>Invalid batch year.</p>';
}
?>
