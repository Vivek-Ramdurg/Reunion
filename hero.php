<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
    die("Database connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("
    SELECT d.district_name, c.institution_name, u.batch_year, u.institution_id 
    FROM users u
    JOIN districts d ON u.district_id = d.district_id
    LEFT JOIN colleges c ON u.institution_id = c.institution_id
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user info is fetched
if (!$userInfo) {
    die("User information not found.");
}

// Fetch batch years based on college_id (institution_id)
$stmt = $pdo->prepare("
    SELECT b.batch_year 
    FROM batches b
    JOIN colleges_batches cb ON b.batch_id = cb.batch_id
    WHERE cb.institution_id = ?
    GROUP BY b.batch_year
");
$stmt->execute([$userInfo['institution_id']]);
$batches = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reunion</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="hero.css">
</head>
<body>
    <h1>Find Your friends</h1>

    <form id="filterForm">
        <!-- Pre-filled district -->
        <label for="district">District:</label>
        <input type="text" id="district" value="<?= htmlspecialchars($userInfo['district_name']) ?>" readonly>

        <!-- Pre-filled college -->
        <label for="college">College:</label>
        <input type="text" id="college" value="<?= htmlspecialchars($userInfo['institution_name']) ?>" readonly>

        <!-- Select batch -->
        <label for="batch">Select Batch:</label>
        <select id="batch" name="batch" required>
            <option value="">Select a Batch</option>
            <?php foreach ($batches as $batch): ?>
                <option value="<?= htmlspecialchars($batch['batch_year']) ?>"><?= htmlspecialchars($batch['batch_year']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="button" id="search">Search Users</button>
        <a href="dashboard.php">Dashboard</a>
    </form>

    <div id="results"></div>

    <script>
        $(document).ready(function () {
            $('#search').click(function () {
                const batchYear = $('#batch').val();

                if (batchYear) {
                    $.get('search_users.php', {batch_year: batchYear}, function (data) {
                        $('#results').html(data);
                    });
                } else {
                    alert('Please select a batch.');
                }
            });
        });
    </script>
</body>
</html>
