<?php
// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['district_id'], $_GET['college_id'], $_GET['batch_id'])) {
        $district_id = $_GET['district_id'];
        $college_id = $_GET['college_id'];
        $batch_id = $_GET['batch_id'];
 
        // Debugging - Log inputs
        file_put_contents('debug_log.txt', "Inputs - District: $district_id, College: $college_id, Batch: $batch_id\n", FILE_APPEND);

        // Query to fetch friends with enhanced joins
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.full_name, u.email, u.phone
            FROM users u
            JOIN batch_users bu ON u.user_id = bu.user_id
            JOIN colleges_batches cb ON cb.batch_id = bu.batch_id
            WHERE u.district_id = :district_id
              AND cb.institution_id = :college_id
              AND bu.batch_id = :batch_id
        ");
        $stmt->execute([
            ':district_id' => $district_id,
            ':college_id' => $college_id,
            ':batch_id' => $batch_id
        ]);

        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display results
        if ($friends) {
            foreach ($friends as $friend) {
                echo '<p>' . htmlspecialchars($friend['full_name']) . ' (' . htmlspecialchars($friend['email']) .  ')</p>';
            }
        } else {
            echo '<p>No friends found for this selection.</p>';
        }
    } else {
        echo '<p>Invalid request. Missing parameters.</p>';
    }
} catch (PDOException $e) {
    // Debugging - Log error
    file_put_contents('debug_log.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    die("Error: " . $e->getMessage());
}
?>
