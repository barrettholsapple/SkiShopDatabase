<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['original_serialnumber'], $_POST['serialnumber'], $_POST['daterented'], $_POST['returndate'], 
              $_POST['poles'], $_POST['polemake'], $_POST['polesize'], $_POST['boots'], $_POST['bootmake'], 
              $_POST['bootsize'], $_POST['seasonal'], $_POST['bindingdinlt'], $_POST['bindingdinlh'], 
              $_POST['bindingdinrt'], $_POST['bindingdinrh'], $_POST['salesperson'], $_POST['customerid'], $_POST['notes'])
    ) {
        // Retrieve and sanitize input data
        $original_serialnumber = $_POST['original_serialnumber'];
        $serialnumber = $_POST['serialnumber'];
        $daterented = $_POST['daterented'];
        $returndate = $_POST['returndate'];
        $poles = $_POST['poles'];
        $polemake = $_POST['polemake'];
        $polesize = $_POST['polesize'];
        $boots = $_POST['boots'];
        $bootmake = $_POST['bootmake'];
        $bootsize = $_POST['bootsize'];
        $seasonal = $_POST['seasonal'];
        $bindingdinlt = $_POST['bindingdinlt'];
        $bindingdinlh = $_POST['bindingdinlh'];
        $bindingdinrt = $_POST['bindingdinrt'];
        $bindingdinrh = $_POST['bindingdinrh'];
        $salesperson = $_POST['salesperson'];
        $customerid = $_POST['customerid'];
        $notes = $_POST['notes'];

        // Prepare SQL UPDATE statement
        $query = "UPDATE rentals SET 
                    serialnumber = ?, daterented = ?, returndate = ?, poles = ?, polemake = ?, 
                    polesize = ?, boots = ?, bootmake = ?, bootsize = ?, seasonal = ?, 
                    bindingdinlt = ?, bindingdinlh = ?, bindingdinrt = ?, bindingdinrh = ?, 
                    salesperson = ?, customerid = ?, notes = ?
                  WHERE serialnumber = ?";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        // Note: 18 parameters total
        $stmt->bind_param("ssssssssssssssssss", 
            $serialnumber, $daterented, $returndate, $poles, $polemake, 
            $polesize, $boots, $bootmake, $bootsize, $seasonal, 
            $bindingdinlt, $bindingdinlh, $bindingdinrt, $bindingdinrh, 
            $salesperson, $customerid, $notes, $original_serialnumber
        );

        if ($stmt->execute()) {
            header("Location: skirentals.php");
            exit();
        } else {
            echo "Error updating rental: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Required fields are missing.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
