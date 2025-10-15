<!DOCTYPE html>
<html>
<head>
    <title>Added Customer</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("<p>No form data submitted.</p>");
        }

        // Collect POST data safely
        $fname = $_POST['fname'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $streetadd = $_POST['streetadd'] ?? '';
        $city = $_POST['city'] ?? '';
        $stateadd = $_POST['stateadd'] ?? '';
        $zip = $_POST['zip'] ?? '';
        $driversli = $_POST['driversli'] ?? '';
        $rawPhone = $_POST['phonenum'] ?? '';
        $phonenum = preg_replace('/\D/', '', $rawPhone); // Remove all non-digit characters
        $email = $_POST['email'] ?? '';
        $birthday = $_POST['birthday'] ?? '';
        $weightlbs = $_POST['weightlbs'] ?? '';
        $heightft = $_POST['heightft'] ?? '';
        $heightin = $_POST['heightin'] ?? '';
        $skilllevel = $_POST['skilllevel'] ?? '';

        // Connect to DB
        $db = new mysqli("localhost", "root", "", "skishop");
        if ($db->connect_error) {
            die("<p>Unable to connect to DB: " . htmlspecialchars($db->connect_error) . "</p>");
        }

        // Use prepared statement to insert data
        $stmt = $db->prepare("
            INSERT INTO customers 
            (fname, lname, streetadd, city, stateadd, zip, driversli, phonenum, email, birthday, weightlbs, heightft, heightin, skilllevel)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("<p>Prepare failed: " . htmlspecialchars($db->error) . "</p>");
        }

        $stmt->bind_param(
            "ssssssssssiiii",
            $fname, $lname, $streetadd, $city, $stateadd, $zip, $driversli, $phonenum, $email, $birthday,
            $weightlbs, $heightft, $heightin, $skilllevel
        );

        if ($stmt->execute()) {
            echo "<h1>New customer added successfully!</h1>";
        } else {
            echo "<p>Query Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
        $db->close();
        ?>

        <h4>
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </h4>
    </div>
</body>
</html>

