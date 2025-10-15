<!DOCTYPE html>
<html>
<head>
    <title>Added Board Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Board Rental Management</h1>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("<p>No form data submitted.</p>");
        }

        $db = new mysqli("localhost", "root", "", "skishop");
        if ($db->connect_errno) {
            die("<p>Unable to connect to DB: " . htmlspecialchars($db->connect_error) . "</p>");
        }

        $boardserialnumber = $_POST['boardserialnumber'];
        $returndate        = $_POST['returndate'];
        $boots             = $_POST['boots'];
        $bootmake          = $_POST['bootmake'] ?? "";
        $bootsize          = $_POST['bootsize'] ?? "";
        $stance            = $_POST['stance'];
        $seasonal          = $_POST['seasonal'];
        $salesperson       = $_POST['salesperson'];
        $customerid        = $_POST['customerid'];
        $notes             = $_POST['notes'] ?? "";

        $stmt = $db->prepare("INSERT INTO boardrentals (
            boardserialnumber, daterented, returndate, boots, bootmake, bootsize, 
            stance, seasonal, salesperson, customerid, notes
        ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            die("<p>Prepare failed: " . htmlspecialchars($db->error) . "</p>");
        }
        
        $stmt->bind_param(
            "ssisssisis",
            $boardserialnumber,
            $returndate,
            $boots,
            $bootmake,
            $bootsize,
            $stance,       // now correctly treated as string
            $seasonal,
            $salesperson,
            $customerid,
            $notes
        );
        
        

        if ($stmt->execute()) {
            echo "<p class='message'>New Board Rental was successfully added to the database.</p>";
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
