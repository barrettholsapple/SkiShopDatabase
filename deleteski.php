<!DOCTYPE html>
<html>
<head>
    <title>Delete Ski</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h2>Delete Ski</h2>

        <?php
        $conn = new mysqli('localhost', 'root', '', 'skishop');
        if ($conn->connect_error) {
            echo "<p class='error'>Database connection failed: " . htmlspecialchars($conn->connect_error) . "</p>";
        } else {
            if (isset($_GET['serialnumber'])) {
                $serialnumber = $_GET['serialnumber'];

                // Check for rental history
                $checkQuery = "SELECT COUNT(*) FROM pastrentals WHERE serialnumber = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("s", $serialnumber);
                $checkStmt->execute();
                $checkStmt->bind_result($count);
                $checkStmt->fetch();
                $checkStmt->close();

                if ($count > 0) {
                    echo "<p class='error'>⚠️ Cannot delete this ski — it has rental history in the system.</p>";
                } else {
                    $deleteQuery = "DELETE FROM skis WHERE serialnumber = ?";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    $deleteStmt->bind_param("s", $serialnumber);
                    if ($deleteStmt->execute()) {
                        echo "<p class='message'>✅ Ski deleted successfully.</p>";
                    } else {
                        echo "<p class='error'>Error: " . htmlspecialchars($deleteStmt->error) . "</p>";
                    }
                    $deleteStmt->close();
                }
            } else {
                echo "<p class='error'>No serial number provided.</p>";
            }

            $conn->close();
        }
        ?>

        <h4>
            <a href="skisearch.php" class="back-link">
                Back to Ski Search
            </a>
        </h4>
    </div>
</body>
</html>



