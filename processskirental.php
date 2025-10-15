<!DOCTYPE html>
<html>
<head>
    <title>Added Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Rental Management</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("<p>No form data submitted.</p>");
        }

        // Connect to DB
        $db = new mysqli("localhost", "root", "", "skishop");
        if ($db->connect_errno) {
            die("<p>Unable to connect to DB: " . htmlspecialchars($db->connect_error) . "</p>");
        }

        // Collect sanitized POST values
        $serialnumber   = $_POST['serialnumber'];
        $returndate     = $_POST['returndate'];
        $poles          = $_POST['poles'];
        $polemake       = $_POST['polemake'] ?? "";
        $polesize       = $_POST['polesize'] ?? "";
        $boots          = $_POST['boots'];
        $bootmake       = $_POST['bootmake'] ?? "";
        $bootsize       = $_POST['bootsize'] ?? "";
        $seasonal       = $_POST['seasonal'];
        $bindingdinlt   = $_POST['bindingdinlt'] ?? "";
        $bindingdinlh   = $_POST['bindingdinlh'] ?? "";
        $bindingdinrt   = $_POST['bindingdinrt'] ?? "";
        $bindingdinrh   = $_POST['bindingdinrh'] ?? "";
        $salesperson    = $_POST['salesperson'];
        $customerid     = $_POST['customerid'];
        $notes          = $_POST['notes'] ?? "";

        // Use a prepared statement
        $stmt = $db->prepare("
            INSERT INTO rentals (
                serialnumber, daterented, returndate, 
                poles, polemake, polesize, boots, bootmake, bootsize, 
                seasonal, bindingdinlt, bindingdinlh, bindingdinrt, bindingdinrh, 
                salesperson, customerid, notes
            )
            VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("<p>Prepare failed: " . htmlspecialchars($db->error) . "</p>");
        }

        $stmt->bind_param(
            "ssisssssisssssis",
            $serialnumber, $returndate,
            $poles, $polemake, $polesize,
            $boots, $bootmake, $bootsize,
            $seasonal, $bindingdinlt, $bindingdinlh, $bindingdinrt, $bindingdinrh,
            $salesperson, $customerid, $notes
        );

        if ($stmt->execute()) {
            echo "<p class='message'>New Rental was successfully added to the database.</p>";
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
