<?php
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

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email and password from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        // Invalid credentials
        $error_message = "Invalid email or password.";
        
    }
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <h1>Login</h1>

            <?php if (isset($error_message)) : ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Login</button>
            </form>
            <button onclick="location.href='index.html'" class="signin-btn">Home page</button>
            <p><a href="forgot_password.php">Forgot your password?</a></p>

            <p>Don't have an account? <a href="get.php">Register here</a>.</p>
        </div>

        <div class="login-image">
            <img src="pics/—Pngtree—log in login interface computer_3945571.png" alt="Login Image" class="side-image">
        </div>
    </div>
</body>
</html>
