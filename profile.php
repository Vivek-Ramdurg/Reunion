<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$db = 'reunion';
$user = 'root';
$pass = '';
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'] ?? '';
    $profilePhotoPath = null;

    // Handle file upload
    // Handle file upload
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = '/reunion/uploads/';
  $profilePhotoName = basename($_FILES['profile_photo']['name']);
  $profilePhotoPath = $uploadDir . $profilePhotoName;
  
  $fullFilePath = $_SERVER['DOCUMENT_ROOT'] . $profilePhotoPath;
  
  // Move the uploaded file
  if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $fullFilePath)) {
      echo "Failed to upload file.";
  }
  
}


    // Update user profile
    $stmt = $pdo->prepare("
        UPDATE users 
        SET description = ?, profile_photo = COALESCE(?, profile_photo) 
        WHERE user_id = ?
    ");
    $stmt->execute([$description, $profilePhotoPath, $user_id]);

    header("Location: dashboard.php");
    exit;
}

// Fetch current profile data
$stmt = $pdo->prepare("SELECT profile_photo, description FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<style>
    /* General page styles */
body {
  font-family: 'Arial', sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  padding: 0;
}

h1 {
  text-align: center;
  margin-top: 50px;
  color: #333;
}

form {
  background-color: white;
  max-width: 500px;
  margin: 50px auto;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Label styles */
label {
  font-size: 1.1rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 10px;
  display: block;
}

/* Input and textarea styles */
input[type="file"],
textarea {
  width: 100%;
  padding: 12px;
  margin-bottom: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
  color: #333;
}

/* Textarea specific styling */
textarea {
  resize: vertical;
}

/* Button styling */
button {
  background-color: #6c63ff;
  color: white;
  padding: 12px 25px;
  font-size: 1.1rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
  transition: all 0.3s ease;
}

button:hover {
  background-color: #4d47cc;
  transform: translateY(-3px);
}

button:active {
  transform: translateY(2px);
}

/* Link styling */
a {
  color: #6c63ff;
  text-decoration: none;
  font-size: 1rem;
  text-align: center;
  display: inline-block;
  margin-top: 20px;
}

a:hover {
  color: #4d47cc;
}

/* Mobile responsiveness */
@media (max-width: 600px) {
  form {
    padding: 20px;
    width: 90%;
  }

  button {
    padding: 10px 20px;
  }

  a {
    font-size: 0.9rem;
  }
}

</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>


<body>
    <h1>Edit Profile</h1>
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="profile_photo">Profile Photo:</label>
            <input type="file" name="profile_photo" id="profile_photo">
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
        </div>
        <button type="submit">Save Changes</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
