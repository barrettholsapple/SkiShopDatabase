<!DOCTYPE html>
<html>
<head>
    <title>Delete Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h2>Delete Rental Data</h2>
        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'skishop');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Delete rental
        if (isset($_GET['serialnumber'])) {
            $serialnumber = $_GET['serialnumber']; // Get serial number from URL
            $query = "DELETE FROM rentals WHERE serialnumber = ?";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                // Use 's' for string type if serialnumber is alphanumeric
                $stmt->bind_param("s", $serialnumber);
                if ($stmt->execute()) {
                    echo "<p>Rental deleted successfully.</p>";
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
            } else {
                echo "<p>Error preparing statement: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>No serial number provided for deletion.</p>";
        }

        // Close the connection
        $conn->close();
        ?>
        <h4>
            <a href="skisearch.php" class="back-link">
                Back to Ski Search
            </a>
        </h4>
    </div>
</body>
</html>
