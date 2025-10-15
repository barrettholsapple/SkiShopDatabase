<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are present
    if (isset($_POST['boardserialnumber'], $_POST['daterented'], $_POST['returndate'], $_POST['stance'],
              $_POST['boots'], $_POST['bootmake'], $_POST['bootsize'], $_POST['seasonal'], 
              $_POST['salesperson'], $_POST['customerid'], $_POST['notes'])) {

        // Collect and sanitize input
        $boardserialnumber = $_POST['boardserialnumber'];
        $daterented        = $_POST['daterented'];
        $returndate        = $_POST['returndate'];
        $stance            = $_POST['stance'];
        $boots             = $_POST['boots'];
        $bootmake          = $_POST['bootmake'];
        $bootsize          = $_POST['bootsize'];
        $seasonal          = $_POST['seasonal'];
        $salesperson       = $_POST['salesperson'];
        $customerid        = $_POST['customerid'];
        $notes             = $_POST['notes'];

        // Prepare the UPDATE query
        $query = "UPDATE boardrentals SET 
                    daterented = ?, 
                    returndate = ?, 
                    stance = ?, 
                    boots = ?, 
                    bootmake = ?, 
                    bootsize = ?, 
                    seasonal = ?, 
                    salesperson = ?, 
                    customerid = ?, 
                    notes = ?
                  WHERE boardserialnumber = ?";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind the parameters
        $stmt->bind_param("sssssssssis",
            $daterented, $returndate, $stance, $boots, $bootmake, $bootsize,
            $seasonal, $salesperson, $customerid, $notes, $boardserialnumber
        );

        // Execute and handle result
        if ($stmt->execute()) {
            echo "Board rental updated successfully.";
            header("Location: boardrentals.php");
            exit();
        } else {
            echo "Error updating board rental: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Required fields are missing.";
    }
} else {
    echo "Invalid request.";
}

// Close database connection
$conn->close();
?>
