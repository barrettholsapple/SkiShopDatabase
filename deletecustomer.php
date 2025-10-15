<!DOCTYPE html>
<html>
<head>
    <title>Delete Customer</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h2>Delete Customer</h2>
        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'skishop');
        if ($conn->connect_error) {
            die("Connection failed: " . htmlspecialchars($conn->connect_error));
        }

        // Delete customer safely
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $customerid = (int) $_GET['id'];
            $query = "DELETE FROM customers WHERE customerid = ?";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("i", $customerid);
                if ($stmt->execute()) {
                    echo "<p>Customer deleted successfully.</p>";
                } else {
                    echo "<p>Error executing delete: " . htmlspecialchars($stmt->error) . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p>Prepare failed: " . htmlspecialchars($conn->error) . "</p>";
            }
        } else {
            echo "<p>Invalid or missing customer ID.</p>";
        }

        $conn->close();
        ?>

        <h4><a href="customers.php" class="back-link">Back to Customer Search</a></h4>
    </div>
</body>
</html>


