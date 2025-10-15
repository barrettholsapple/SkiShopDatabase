<!DOCTYPE html>
<html>
<head>
    <title>Delete Board</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h2>Delete Board from Database</h2>

        <?php
        $conn = new mysqli('localhost', 'root', '', 'skishop');
        if ($conn->connect_error) {
            echo "<p class='error'>Database connection failed: " . htmlspecialchars($conn->connect_error) . "</p>";
        } else {
            if (isset($_GET['boardserialnumber'])) {
                $boardserialnumber = $_GET['boardserialnumber'];

                // Check for rental history
                $checkQuery = "SELECT COUNT(*) FROM boardrentals WHERE boardserialnumber = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("s", $boardserialnumber);
                $checkStmt->execute();
                $checkStmt->bind_result($count);
                $checkStmt->fetch();
                $checkStmt->close();

                if ($count > 0) {
                    echo "<p class='error'>⚠️ Cannot delete this board — it has rental history in the system.</p>";
                } else {
                    $deleteQuery = "DELETE FROM boards WHERE boardserialnumber = ?";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    $deleteStmt->bind_param("s", $boardserialnumber);
                    if ($deleteStmt->execute()) {
                        echo "<p class='message'>✅ Board deleted successfully.</p>";
                    } else {
                        echo "<p class='error'>Error: " . htmlspecialchars($deleteStmt->error) . "</p>";
                    }
                    $deleteStmt->close();
                }
            } else {
                echo "<p class='error'>No board serial number provided.</p>";
            }

            $conn->close();
        }
        ?>

        <h4>
            <a href="boardsearch.php" class="back-link">Back to Board Search</a>
        </h4>
    </div>
</body>
</html>

