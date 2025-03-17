<?php
// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['district_id'])) {
    $district_id = $_GET['district_id'];
    $stmt = $pdo->prepare("SELECT institution_id, institution_name FROM colleges WHERE district_id = ?");
    $stmt->execute([$district_id]);
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($colleges) {
        echo '<option value="">Select a College</option>';
        foreach ($colleges as $college) {
            echo '<option value="' . $college['institution_id'] . '">' . $college['institution_name'] . '</option>';
        }
    } else {
        echo '<option value="">No colleges found</option>';
    }
}
?>
