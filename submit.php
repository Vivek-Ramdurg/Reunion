<?php
// Database connection variables
$servername = "localhost"; // Use your server name
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "reunion23";     // New database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the input values from the form
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $occupation = $_POST['occupation'];
    $city = $_POST['city'];
    $institution = $_POST['institution'];
    $grad_year = $_POST['grad-year'];

    // Prepare the SQL query
    $sql = "INSERT INTO users23 (first_name, last_name, email, password, occupation, city, institution, grad_year) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error in preparing the statement: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $password, $occupation, $city, $institution, $grad_year);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Form submitted successfully!";
    } else {
        echo "Error executing the query: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: For adding CSS styles -->
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: For adding CSS styles -->
</head>
<body>

    <form action="submit.php" method="post" class="get-started-form">
        <div class="container">
            <h2>Get Started</h2>

            <!-- First Name Field -->
            <label for="first-name">Enter your first name:</label>
            <input type="text" id="first-name" name="first-name" placeholder="First Name" required>

            <!-- Last Name Field -->
            <label for="last-name">Enter your last name:</label>
            <input type="text" id="last-name" name="last-name" placeholder="Last Name" required>

            <!-- Email Field -->
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email" placeholder="Email Address" required>

            <!-- Password Field -->
            <label for="password">Create a password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <!-- Occupation Field -->
            <label for="occupation">Enter your occupation:</label>
            <input type="text" id="occupation" name="occupation" placeholder="Occupation" required>

            <!-- City Field -->
            <label for="city">Enter the city you live in:</label>
            <input type="text" id="city" name="city" placeholder="City" required>

            <!-- Institution Dropdown -->
            <select id="institution" name="institution" required>
                <option value="">Select Institution</option>
                <option value="010">KLE Society's College of Pharmacy</option>
                <option value="011">KLES College of Pharmacy Nipani</option>
                <option value="012">Hirasugar Institute of Technology</option>
                <option value="013">DBHPS Dr BD Jatti College of Education</option>
                <option value="014">GES College of Education Gokak</option>
                <option value="015">KLS Gogte Institute of Technology</option>
                <option value="016">KLE Society JL Nehru Medical College</option>
                <option value="017">Maratha Mandal College of Pharmacy</option>
                <option value="018">Aadya Shri Nigalingeshwar Shikshana Sansthe S B Shirkoli Homeopathic Medical College</option>
                <option value="019">Bhartesh Homeopathic Medical College</option>
                <option value="020">Jawaharlal Nehru Medical College</option>
                <option value="021">KLES BV Bellad Law College</option>
                <option value="022">KLES RL Science Institute</option>
                <option value="023">JSS Arts Science and Commerce College</option>
                <option value="024">Anjuman-E-Islam Arts & Commerce College</option>
                <option value="025">BK College of Arts Science & Commerce</option>
                <option value="026">Trident Institute of Management Sciences</option>
                <option value="027">Annapoorna Institute of Management Research (AIMR)</option>
                <option value="028">Global Business School, Belgaum</option>
                <option value="029">School Of Applied Sciences, RCUB</option>
                <option value="030">KLE University Institute of Physiotherapy</option>
                <option value="031">School Of Mathematics & Computing Science, RCUB</option>
                <option value="032">Govindram Seksaria Science College</option>
                <option value="033">Mahaveer P Mirji College of Commerce</option>
                <option value="034">DVVS Arts College & T.P.Science Institute</option>
                <option value="035">Arts and Commerce College</option>
                <option value="036">Gogte College of Commerce</option>
                <option value="037">Raja Lakhamgouda Law College</option>
                <option value="038">Raja Lakhamagouda Science Institute</option>
                <option value="039">KLE Societys Basavaprabhu Kore Arts Science and Commerce College chikodi</option>
                <option value="040">Bhaurao Kakatkar College</option>
                <option value="041">CTES Smt.Ahalyabai Appanagouda Patil Arts & Commerce College for Women chikkodi</option>
                <option value="042">School of Education, RCUB</option>
                <option value="043">School Of Criminology & Criminal Justice, RCUB</option>
                <option value="044">School of Business and Economics, RCUB</option>
                <option value="045">School Of Social Sciences, RCUB</option>
                <option value="046">School Of Classical Kannada Studies, RCUB</option>
                <option value="047">School of Languages, RCUB</option>
                <option value="048">Gomatesh Polytechnics College</option>
                <option value="049">Gomatesh College of Business Administration</option>
                <option value="050">Gomatesh College Of Bachelors Of Computer Application</option>
                <option value="051">Gomatesh Ayurvedic Medical College</option>
                <option value="052">H H Bhadrabahu Swami School of Nursing</option>
                <option value="053">Gomatesh Teachers Training College</option>
                <option value="054">RN Shetty Polytechnic</option>
                <option value="055">K L E Society College of Nursing</option>
                <option value="056">S.C.S.E.S. Shiv Basav Jyoti Homeopathic Medical College</option>
                <option value="057">Rani Parvati Devi College of Arts & Commerce</option>
                <option value="058">KLS Institute of Management Education & Research</option>
                <option value="059">Belgaum Institute of Management Studies</option>
                <option value="060">V S M Institude Of Technology. Chikodi</option>
                <option value="061">PES College Of BBA Ugar Khurd</option>
                <option value="062">KLE Societys Lingraj College</option>
                <option value="063">Birds Bsw College</option>
                <option value="064">Shri Siddhivinayaka Rural PG Center</option>
                <option value="065">PG Center</option>
                <option value="066">Shri Laxmanrao Jarkihole Law College</option>
                <option value="067">KLE Law College Chikodi</option>
                <option value="068">ASNSs Mahatma Gandhi Ji Law College</option>
                <option value="069">HV Koujalagi Law College</option>
                <option value="070">Shree Shivayogeeshwar Ayurvedic Medical College</option>
                <option value="071">Shri Shivayogeshwar Rural Ayurvedic Medical College</option>
                <option value="072">KLES Institute of Nursing Sciences</option>
                <option value="073">KRC College of Horticulture Arabhavi</option>
                <option value="074">KR & E.D. Associations Rani Chennamma College of Pharmacy</option>
                <option value="075">ASNSSS Sanjay Patil College of Pharmacy</option>
                <option value="076">Shri BM Kankanwadi Ayurved Medical College</option>
                <option value="077">Chousan College of Education</option>
                <option value="078">DMSMs B. K. College of Commerce</option>
                <option value="079">Dr. B. R. Ambedkar Shikshana Samsthe B.Shankaranand Arts College</option>
                <option value="080">GIB Arts Science & Commerce College</option>
                <option value="081">Govt. college of Education</option>
                <option value="082">Govt. Ist Grade College Nesaragi</option>
            </select>

            <!-- Graduation Year Field -->
            <label for="grad-year">Which year did you pass out in?</label>
            <input type="number" id="grad-year" name="grad-year" placeholder="Year" required>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary">Submit</button>
        </div>
    </form>

</body>
</html>


</body>
</html>
