<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_code = $_POST['otp'];
    $email = $_SESSION['email'];

    // Check OTP
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ?");
    $stmt->execute([$email, $otp_code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Mark as verified
        $update_stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE, otp_code = NULL WHERE email = ?");
        $update_stmt->execute([$email]);

        echo "Email verified successfully!";
        unset($_SESSION['email']);
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
</head>
<body>
    <form method="POST">
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
