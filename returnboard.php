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
    $boardserialnumber = $_POST['boardserialnumber'];

    $stmt = $conn->prepare("SELECT * FROM boardrentals WHERE customerid = ? AND boardserialnumber = ?");
    $stmt->bind_param("ss", $customerid, $boardserialnumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $rental = $result->fetch_assoc();

    if ($rental) {
        $returnsalesperson = 'unknown'; // Update if you plan to track this

        $insertQuery = "INSERT INTO pastboardrentals (
            boardserialnumber, daterented, returndate, datereturned,
            boots, bootmake, bootsize,
            stance, seasonal, salesperson, returnsalesperson,
            customerid, notes
        ) VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            die("Insert prepare failed: " . $conn->error);
        }

        $insertStmt->bind_param("ssssssssssis",
            $rental['boardserialnumber'],
            $rental['daterented'],
            $rental['returndate'],
            $rental['boots'],
            $rental['bootmake'],
            $rental['bootsize'],
            $rental['stance'],
            $rental['seasonal'],
            $rental['salesperson'],
            $returnsalesperson,
            $rental['customerid'],
            $rental['notes']
        );

        if ($insertStmt->execute()) {
            $deleteStmt = $conn->prepare("DELETE FROM boardrentals WHERE customerid = ? AND boardserialnumber = ?");
            $deleteStmt->bind_param("ss", $customerid, $boardserialnumber);
            $deleteStmt->execute();
            $message = "Board rental successfully returned.";
        } else {
            $message = "Error inserting into pastboardrentals: " . $insertStmt->error;
        }
    } else {
        $message = "Rental not found.";
    }
}

// Get active board rentals
function getActiveBoardRentals($conn, $customerid) {
    $query = "SELECT r.boardserialnumber, b.boardmake, b.boardmodel, r.daterented, r.returndate 
              FROM boardrentals r
              JOIN boards b ON r.boardserialnumber = b.boardserialnumber
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
    <title>Return Board Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>Return Snowboard Rental</h1>

    <?php if (isset($message)) echo "<p style='color: #f5b380;'>$message</p>"; ?>

    <?php
    if (!empty($customerid)) {
        $result = getActiveBoardRentals($conn, $customerid);

        $stmt = $conn->prepare("SELECT fname, lname FROM customers WHERE customerid = ?");
        $stmt->bind_param("s", $customerid);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();

        echo "<h2>Active Rentals for {$customer['lname']}, {$customer['fname']}</h2>";

        if ($result->num_rows > 0) {
            echo "<form method='POST'><table><tr><th>Snowboard</th><th>Rental Period</th><th>Return</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['boardmake']} {$row['boardmodel']} (Serial: {$row['boardserialnumber']})</td>
                        <td>{$row['daterented']} to {$row['returndate']}</td>
                        <td>
                            <input type='hidden' name='customerid' value='$customerid'>
                            <input type='hidden' name='boardserialnumber' value='{$row['boardserialnumber']}'>
                            <button type='submit' name='return_rental'>Return</button>
                        </td>
                      </tr>";
            }
            echo "</table></form>";
        } else {
            echo "<p>No active snowboard rentals found for this customer.</p>";
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
