<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get customer ID
$customerid = isset($_GET['customerid']) ? $_GET['customerid'] : '';

// DB Connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle return
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_rental'])) {
    $customerid = $_POST['customerid'];
    $serialnumber = $_POST['serialnumber'];

    $stmt = $conn->prepare("SELECT * FROM rentals WHERE customerid = ? AND serialnumber = ?");
    $stmt->bind_param("ss", $customerid, $serialnumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $rental = $result->fetch_assoc();

    if ($rental) {
        $returnsalesperson = 'unknown'; // Placeholder — update if you plan to track this

        $insertQuery = "INSERT INTO pastrentals (
            serialnumber, daterented, returndate, datereturned,
            poles, polemake, polesize,
            boots, bootmake, bootsize,
            seasonal, bindingdinlt, bindingdinlh, bindingdinrt, bindingdinrh,
            salesperson, returnsalesperson,
            customerid, notes
        ) VALUES (?, ?, ?, CURDATE(),
                  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssssssssssssssssss",
            $rental['serialnumber'],
            $rental['daterented'],
            $rental['returndate'],
            $rental['poles'],
            $rental['polemake'],
            $rental['polesize'],
            $rental['boots'],
            $rental['bootmake'],
            $rental['bootsize'],
            $rental['seasonal'],
            $rental['bindingdinlt'],
            $rental['bindingdinlh'],
            $rental['bindingdinrt'],
            $rental['bindingdinrh'],
            $rental['salesperson'],
            $returnsalesperson,
            $rental['customerid'],
            $rental['notes']
        );

        if ($insertStmt->execute()) {
            $deleteStmt = $conn->prepare("DELETE FROM rentals WHERE customerid = ? AND serialnumber = ?");
            $deleteStmt->bind_param("ss", $customerid, $serialnumber);
            $deleteStmt->execute();
            $message = "Ski rental successfully returned.";
        } else {
            $message = "Error inserting into pastrentals: " . $insertStmt->error;
        }
    } else {
        $message = "Rental not found.";
    }
}

// Function: get active rentals
function getActiveRentals($conn, $customerid) {
    $query = "SELECT r.serialnumber, s.make, s.model, r.daterented, r.returndate 
              FROM rentals r
              JOIN skis s ON r.serialnumber = s.serialnumber
              WHERE r.customerid = ? AND (r.returndate IS NULL OR r.returndate >= CURDATE())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $customerid);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Return Ski Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>Return Ski Rental</h1>

    <?php if (isset($message)) echo "<p style='color: #f5b380;'>$message</p>"; ?>

    <?php
    if (!empty($customerid)) {
        $result = getActiveRentals($conn, $customerid);

        $stmt = $conn->prepare("SELECT fname, lname FROM customers WHERE customerid = ?");
        $stmt->bind_param("s", $customerid);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();

        echo "<h2>Active Rentals for {$customer['lname']}, {$customer['fname']}</h2>";

        if ($result->num_rows > 0) {
            echo "<form method='POST'><table><tr><th>Ski</th><th>Rental Period</th><th>Return</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['make']} {$row['model']} (Serial: {$row['serialnumber']})</td>
                        <td>{$row['daterented']} to {$row['returndate']}</td>
                        <td>
                            <input type='hidden' name='customerid' value='$customerid'>
                            <input type='hidden' name='serialnumber' value='{$row['serialnumber']}'>
                            <button type='submit' name='return_rental'>Return</button>
                        </td>
                      </tr>";
            }
            echo "</table></form>";
        } else {
            echo "<p>No active ski rentals found for this customer.</p>";
        }
    } else {
        echo "<p>No customer selected.</p>";
    }
    ?>
    <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
</div>
</body>
</html>

<?php $conn->close(); ?>


