<?php
// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Capture form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $district_id = $_POST['district'];
    $institution_id = $_POST['college'];
    $batch_year = $_POST['batch_year'];

    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password_hash, phone, district_id, institution_id, batch_year)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$full_name, $email, $password, $phone, $district_id, $institution_id, $batch_year]);

    // Get the newly registered user ID
    $new_user_id = $pdo->lastInsertId();

    // Notify other users in the same batch
    $stmt = $pdo->prepare("
        SELECT user_id FROM users 
        WHERE batch_year = ? AND institution_id = ? AND user_id != ?
    ");
    $stmt->execute([$batch_year, $institution_id, $new_user_id]);
    $users_in_batch = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insert notifications for these users
    foreach ($users_in_batch as $user) {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message) 
            VALUES (?, ?)
        ");
        $stmt->execute([$user['user_id'], "A new user, $full_name, has joined your batch!"]);
    }

    // Show a success message instead of redirecting immediately
    echo "<h1>Thank you for registering, $full_name!</h1>";
    echo "<p>Your registration was successful. You can now <a href='login.php'>log in</a> to your account.</p>";

    
    header("Location: dashboard.php");
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
