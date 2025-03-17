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
    die("Database connection failed: " . $e->getMessage());
}

if (isset($_GET['college_id'])) {
    $college_id = $_GET['college_id'];

    // Query to fetch batch years associated with the selected college
    $stmt = $pdo->prepare("
        SELECT DISTINCT b.batch_year 
        FROM batches b
        INNER JOIN colleges_batches cb ON b.batch_id = cb.batch_id
        WHERE cb.institution_id = ?
        ORDER BY b.batch_year ASC
    ");
    $stmt->execute([$college_id]);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($batches) {
        echo '<option value="">Select a Batch Year</option>';
        foreach ($batches as $batch) {
            echo '<option value="' . htmlspecialchars($batch['batch_year']) . '">' . htmlspecialchars($batch['batch_year']) . '</option>';
        }
    } else {
        echo '<option value="">No batches found</option>';
    }
}
?>
