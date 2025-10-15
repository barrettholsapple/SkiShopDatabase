<!DOCTYPE html>
<html>
<head>
    <title>Added Ski</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Ski Management</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("<p>No form data submitted.</p>");
        }

        // Collect form data
        $serialnumber = $_POST['serialnumber'];
        $make = $_POST['make'];
        $model = $_POST['model'];
        $sizeof = $_POST['sizeof'];
        $notes = $_POST['notes'] ?? "";

        // Connect to DB
        $db = new mysqli("localhost", "root", "", "skishop");
        if ($db->connect_errno) {
            die("<p>Unable to connect to DB: " . htmlspecialchars($db->connect_error) . "</p>");
        }

        // Prepare insert statement
        $stmt = $db->prepare("INSERT INTO skis (serialnumber, make, model, sizeof, notes) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("<p>Prepare failed: " . htmlspecialchars($db->error) . "</p>");
        }

        $stmt->bind_param("sssss", $serialnumber, $make, $model, $sizeof, $notes);

        if ($stmt->execute()) {
            echo "<p class='message'>New ski was successfully added to the database.</p>";
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
        $db->close();
        ?>

        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>

