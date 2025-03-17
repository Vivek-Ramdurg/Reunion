<?php
// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch districts
    $districts = $pdo->query("SELECT * FROM districts")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="get.css">
</head>

<body>
    <!-- <h1>Register User</h1> -->
    <form id="registerForm" method="POST" action="register_user.php">
        <!-- District Selection -->
        <label for="district">Select District:</label>
        <select id="district" name="district" required>
            <option value="">Select a District</option>
            <?php foreach ($districts as $district): ?>
                <option value="<?php echo $district['district_id']; ?>">
                    <?php echo $district['district_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- College Selection -->
        <label for="college">Select College:</label>
        <select id="college" name="college" required>
            <option value="">Select a College</option>
        </select>

        <!-- Batch Year Selection -->
        <label for="batch_year">Select Batch Year:</label>
        <select id="batch_year" name="batch_year" required>
            <option value="">Select a Batch Year</option>
        </select>

        <!-- User Details -->
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required>

        <button type="submit">Register</button>
        <button onclick="location.href='index.html'" class="signin-btn">Home page</button>
    </form>

    <script>
        // Load colleges based on district selection
        $('#district').change(function () {
            var district_id = $(this).val();
            if (district_id) {
                $.ajax({
                    url: 'get_colleges.php',
                    type: 'GET',
                    data: { district_id: district_id },
                    success: function (data) {
                        $('#college').html(data);
                        $('#batch_year').html('<option value="">Select a Batch Year</option>'); // Reset batch year dropdown
                    }
                });
            } else {
                $('#college').html('<option value="">Select a College</option>');
                $('#batch_year').html('<option value="">Select a Batch Year</option>');
            }
        });

        // Load batch years based on college selection
        $('#college').change(function () {
            var college_id = $(this).val();
            if (college_id) {
                $.ajax({
                    url: 'get_batches.php',
                    type: 'GET',
                    data: { college_id: college_id },
                    success: function (data) {
                        $('#batch_year').html(data);
                    }
                });
            } else {
                $('#batch_year').html('<option value="">Select a Batch Year</option>');
            }
        });
    </script>
</body>
</html>
