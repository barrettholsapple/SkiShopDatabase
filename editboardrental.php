<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get boardserialnumber from URL
$boardserialnumber = $_GET['boardserialnumber'] ?? '';
if (empty($boardserialnumber)) {
    die("No board serial number provided.");
}

// Fetch rental data with customer info
$rental = [];
$query = "SELECT r.*, c.fname, c.lname 
          FROM boardrentals AS r 
          LEFT JOIN customers AS c ON r.customerid = c.customerid 
          WHERE r.boardserialnumber = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $boardserialnumber);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Query failed: " . $stmt->error);
}
if ($result->num_rows === 0) {
    die("No rental found for board serial number: " . htmlspecialchars($boardserialnumber));
}
$rental = $result->fetch_assoc();
$stmt->close();

// Get all board serial numbers
$serialnumbers = [];
$serialResult = $conn->query("SELECT boardserialnumber FROM boards");
if ($serialResult) {
    while ($row = $serialResult->fetch_assoc()) {
        $serialnumbers[] = $row['boardserialnumber'];
    }
}

// Get all customers
$customers = [];
$customerResult = $conn->query("SELECT customerid, fname, lname FROM customers");
if ($customerResult) {
    while ($row = $customerResult->fetch_assoc()) {
        $customers[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Snowboard Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>View Snowboard Rental Information</h1>
    <table>
        <tr>
            <th>Board Serial Number:</th>
            <td>
                <a href="editboard.php?boardserialnumber=<?php echo urlencode($rental['boardserialnumber']); ?>">
                    <?php echo htmlspecialchars($rental['boardserialnumber']); ?>
                </a>
            </td>
        </tr>
        <tr>
            <th>Customer:</th>
            <td>
                <a href="editcustomer.php?id=<?php echo urlencode($rental['customerid']); ?>">
                    <?php echo htmlspecialchars($rental['fname'] . ' ' . $rental['lname']); ?>
                </a>
            </td>
        </tr>
        <tr><th>Date Rented:</th><td><?php echo $rental['daterented'] ?? 'N/A'; ?></td></tr>
        <tr><th>Return Date:</th><td><?php echo $rental['returndate'] ?? 'N/A'; ?></td></tr>
        <tr><th>Boots:</th><td><?php echo ($rental['boots'] == 'Y') ? 'Yes' : 'No'; ?></td></tr>
        <tr><th>Boot Size:</th><td><?php echo $rental['bootsize'] ?? 'N/A'; ?></td></tr>
        <tr><th>Boot Make:</th><td><?php echo $rental['bootmake'] ?? 'N/A'; ?></td></tr>
        <tr><th>Stance:</th>
            <td>
                <?php
                if ($rental['stance'] == 'R') {
                    echo 'Regular';
                } elseif ($rental['stance'] == 'G') {
                    echo 'Goofy';
                } else {
                    echo 'N/A';
                }
                ?>
            </td>
        </tr>
        <tr><th>Seasonal:</th><td><?php echo ($rental['seasonal'] == '1') ? 'Seasonal' : 'Daily'; ?></td></tr>
        <tr><th>Salesperson:</th><td><?php echo $rental['salesperson'] ?? 'N/A'; ?></td></tr>
        <tr><th>Notes:</th><td><?php echo $rental['notes'] ?? 'N/A'; ?></td></tr>
    </table>

    <h2>Edit Rental Information</h2>
    <form method="POST" action="updateboardrental.php">
        <input type="hidden" name="boardserialnumber" value="<?php echo htmlspecialchars($rental['boardserialnumber']); ?>" />
        <table>
            <tr>
                <th>Customer:</th>
                <td>
                    <select name="customerid" required>
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer) { ?>
                            <option value="<?php echo $customer['customerid']; ?>"
                                <?php echo ($customer['customerid'] == $rental['customerid']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['fname'] . ' ' . $customer['lname']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr><th>Date Rented:</th><td><input type="date" name="daterented" value="<?php echo $rental['daterented']; ?>"></td></tr>
            <tr><th>Return Date:</th><td><input type="date" name="returndate" value="<?php echo $rental['returndate']; ?>"></td></tr>
            <tr>
                <th>Boots:</th>
                <td>
                    <div class="radio-group">
                        <label><input type="radio" name="boots" value="1" <?php echo ($rental['boots'] == '1') ? 'checked' : ''; ?>> Yes</label>
                        <label><input type="radio" name="boots" value="0" <?php echo ($rental['boots'] == '0') ? 'checked' : ''; ?>> No</label>
                    </div>
                </td>
            </tr>
            <tr><th>Boot Size:</th><td><input type="text" name="bootsize" value="<?php echo $rental['bootsize']; ?>"></td></tr>
            <tr><th>Boot Make:</th><td><input type="text" name="bootmake" value="<?php echo $rental['bootmake']; ?>"></td></tr>
            <tr>
                <th>Stance:</th>
                <td>
                    <div class="radio-group">
                        <label><input type="radio" name="stance" value="R" <?php echo ($rental['stance'] == 'R') ? 'checked' : ''; ?>> Regular</label>
                        <label><input type="radio" name="stance" value="G" <?php echo ($rental['stance'] == 'G') ? 'checked' : ''; ?>> Goofy</label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Rental Type:</th>
                <td>
                    <div class="radio-group">
                        <label><input type="radio" name="seasonal" value="1" <?php echo ($rental['seasonal'] == '1') ? 'checked' : ''; ?>> Seasonal</label>
                        <label><input type="radio" name="seasonal" value="0" <?php echo ($rental['seasonal'] == '0') ? 'checked' : ''; ?>> Daily</label>
                    </div>
                </td>
            </tr>
            <tr><th>Salesperson:</th><td><input type="text" name="salesperson" value="<?php echo $rental['salesperson']; ?>"></td></tr>
            <tr><th>Notes:</th><td><input type="text" name="notes" value="<?php echo $rental['notes']; ?>"></td></tr>
        </table>
        <br>
        <input type="submit" value="Update Rental">
    </form>

    <h4><a href="boardrentals.php" class="back-link">Back to Rental Page</a></h4>
</div>
</body>
</html>
