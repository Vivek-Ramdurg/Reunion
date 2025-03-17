<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reunion.IC</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Reunion.IC</h1>
        <form id="district-form">
            <label for="district">Select District:</label>
            <select id="district" name="district">
                <option value="">--Select District--</option>
            </select>
        </form>
        <form id="institution-form" style="display: none;">
            <label for="institution">Select School/College:</label>
            <select id="institution" name="institution">
                <option value="">--Select Institution--</option>
            </select>
        </form>
        <form id="batch-form" style="display: none;">
            <label for="batch">Enter Batch Year:</label>
            <input type="number" id="batch" name="batch" placeholder="e.g., 2010">
            <button type="button" id="batch-submit">Search</button>
        </form>
        <div id="batch-results" style="display: none;"></div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=reunion', 'root', '');
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->query("SELECT  district_name FROM districts");
    $districts = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($districts);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
