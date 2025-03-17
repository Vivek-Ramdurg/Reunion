<?php
// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $district_id = $_POST['district_id'];
    $college_id = $_POST['college_id'];
    $batch_year = $_POST['batch_year'];

    $stmt = $pdo->prepare("
        SELECT u.full_name, u.email, u.phone 
        FROM users u
        WHERE u.district_id = ? AND u.institution_id = ? AND u.batch_year = ?
        ORDER BY u.full_name
    ");
    $stmt->execute([$district_id, $college_id, $batch_year]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        echo '<ul>';
        foreach ($users as $user) {
            echo '<li>' . htmlspecialchars($user['full_name']) . ' - ' . htmlspecialchars($user['email']) . ' - ' . htmlspecialchars($user['phone']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No users found for the selected criteria.</p>';
    }
}
?>
