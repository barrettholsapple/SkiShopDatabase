<!DOCTYPE html>
<html>
<head>
    <title>Added Board</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Board Management</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("<p>No form data submitted.</p>");
        }

        // Collect form data
        $boardserialnumber = $_POST['boardserialnumber'];
        $boardmake = $_POST['boardmake'];
        $boardmodel = $_POST['boardmodel'];
        $boardsize = $_POST['boardsize'];
        $boardnotes = $_POST['boardnotes'] ?? "";

        // Connect to DB
        $db = new mysqli("localhost", "root", "", "skishop");
        if ($db->connect_errno) {
            die("<p>Unable to connect to DB: " . htmlspecialchars($db->connect_error) . "</p>");
        }

        // Prepare insert statement
        $stmt = $db->prepare("INSERT INTO boards (boardserialnumber, boardmake, boardmodel, boardsize, boardnotes) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("<p>Prepare failed: " . htmlspecialchars($db->error) . "</p>");
        }

        $stmt->bind_param("sssss", $boardserialnumber, $boardmake, $boardmodel, $boardsize, $boardnotes);

        if ($stmt->execute()) {
            echo "<p class='message'>New board was successfully added to the database.</p>";
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