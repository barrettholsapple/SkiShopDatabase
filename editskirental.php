<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get serialnumber from URL
$serialnumber = $_GET['serialnumber'] ?? '';
if (empty($serialnumber)) {
    die("No serial number provided.");
}

// Fetch rental data with customer info
$rental = [];
$query = "SELECT r.*, c.fname, c.lname 
          FROM rentals AS r 
          LEFT JOIN customers AS c ON r.customerid = c.customerid 
          WHERE r.serialnumber = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $serialnumber); // use "i" if serialnumber is INT
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Query failed: " . $stmt->error);
}
if ($result->num_rows === 0) {
    die("No rental found for serial number: " . htmlspecialchars($serialnumber));
}
$rental = $result->fetch_assoc();
$stmt->close();

// Get all serial numbers
$serialnumbers = [];
$serialResult = $conn->query("SELECT serialnumber FROM boards");
if ($serialResult) {
    while ($row = $serialResult->fetch_assoc()) {
        $serialnumbers[] = $row['serialnumber'];
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
$skis = [];
$skiResult = $conn->query("SELECT serialnumber, make, model, sizeof FROM skis");  // Adjust table/column names if needed
if ($skiResult) {
    while ($row = $skiResult->fetch_assoc()) {
        $skis[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Ski Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>View Rental Information</h1>
    <table>
        <tr>
            <th>Serial Number:</th>
            <td>
                <a href="editski.php?serialnumber=<?php echo urlencode($rental['serialnumber']); ?>">
                    <?php echo htmlspecialchars($rental['serialnumber']); ?>
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
        <tr><th>Binding DIN Left Toe:</th><td><?php echo $rental['bindingdinlt'] ?? 'N/A'; ?></td></tr>
        <tr><th>Binding DIN Left Heel:</th><td><?php echo $rental['bindingdinlh'] ?? 'N/A'; ?></td></tr>
        <tr><th>Binding DIN Right Toe:</th><td><?php echo $rental['bindingdinrt'] ?? 'N/A'; ?></td></tr>
        <tr><th>Binding DIN Right Heel:</th><td><?php echo $rental['bindingdinrh'] ?? 'N/A'; ?></td></tr>
        <tr><th>Date Rented:</th><td><?php echo $rental['daterented'] ?? 'N/A'; ?></td></tr>
        <tr><th>Return Date:</th><td><?php echo $rental['returndate'] ?? 'N/A'; ?></td></tr>
        <tr><th>Poles:</th><td><?php echo ($rental['poles'] == '1') ? 'Yes' : 'No'; ?></td></tr>
        <tr><th>Pole Size:</th><td><?php echo $rental['polesize'] ?? 'N/A'; ?></td></tr>
        <tr><th>Pole Notes:</th><td><?php echo $rental['polemake'] ?? 'N/A'; ?></td></tr>
        <tr><th>Boots:</th><td><?php echo ($rental['boots'] == '1') ? 'Yes' : 'No'; ?></td></tr>
        <tr><th>Boot Size:</th><td><?php echo $rental['bootsize'] ?? 'N/A'; ?></td></tr>
        <tr><th>Boot Length:</th><td><?php echo $rental['bootmake'] ?? 'N/A'; ?></td></tr>
        <tr><th>Seasonal:</th><td><?php echo ($rental['seasonal'] == '1') ? 'Seasonal' : 'Daily'; ?></td></tr>
        <tr><th>Salesperson:</th><td><?php echo $rental['salesperson'] ?? 'N/A'; ?></td></tr>
        <tr><th>Notes:</th><td><?php echo $rental['notes'] ?? 'N/A'; ?></td></tr>
    </table>

    <h2>Edit Rental Information</h2>
    <form method="POST" action="update_rental.php">
        <input type="hidden" name="serialnumber" value="<?php echo htmlspecialchars($rental['serialnumber']); ?>" />
        <table>
            <input type="hidden" name="original_serialnumber" value="<?php echo htmlspecialchars($rental['serialnumber']); ?>" />
            <tr>
                <th>Ski:</th>
                <td>
                    <select name="serialnumber" required>
                        <option value="">Select Ski</option>
                        <?php foreach ($skis as $ski) { ?>
                            <option value="<?php echo $ski['serialnumber']; ?>"
                                <?php echo ($ski['serialnumber'] == $rental['serialnumber']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars("{$ski['serialnumber']} - {$ski['make']} {$ski['model']} ({$ski['sizeof']})"); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
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
            <tr><th>Binding DIN Left Toe:</th><td><input type="text" name="bindingdinlt" value="<?php echo $rental['bindingdinlt']; ?>"></td></tr>
            <tr><th>Binding DIN Left Heel:</th><td><input type="text" name="bindingdinlh" value="<?php echo $rental['bindingdinlh']; ?>"></td></tr>
            <tr><th>Binding DIN Right Toe:</th><td><input type="text" name="bindingdinrt" value="<?php echo $rental['bindingdinrt']; ?>"></td></tr>
            <tr><th>Binding DIN Right Heel:</th><td><input type="text" name="bindingdinrh" value="<?php echo $rental['bindingdinrh']; ?>"></td></tr>
            <tr><th>Date Rented:</th><td><input type="date" name="daterented" value="<?php echo $rental['daterented']; ?>"></td></tr>
            <tr><th>Return Date:</th><td><input type="date" name="returndate" value="<?php echo $rental['returndate']; ?>"></td></tr>
            <tr>
                <th>Poles:</th>
                <td class="radio-group">
                    <label><input type="radio" name="poles" value="1" <?php echo ($rental['poles'] == '1') ? 'checked' : ''; ?>> Yes</label>
                    <label><input type="radio" name="poles" value="0" <?php echo ($rental['poles'] == '0') ? 'checked' : ''; ?>> No</label>
                </td>
            </tr>
            <tr><th>Pole Size:</th><td><input type="text" name="polesize" value="<?php echo $rental['polesize']; ?>"></td></tr>
            <tr><th>Pole Notes:</th><td><input type="text" name="polemake" value="<?php echo $rental['polemake']; ?>"></td></tr>
            <tr>
                <th>Boots:</th>
                <td class="radio-group">
                    <label><input type="radio" name="boots" value="1" <?php echo ($rental['boots'] == '1') ? 'checked' : ''; ?>> Yes</label>
                    <label><input type="radio" name="boots" value="0" <?php echo ($rental['boots'] == '0') ? 'checked' : ''; ?>> No</label>
                </td>
            </tr>
            <tr><th>Boot Number:</th><td><input type="text" name="bootsize" value="<?php echo $rental['bootsize']; ?>"></td></tr>
            <tr><th>Boot Length:</th><td><input type="text" name="bootmake" value="<?php echo $rental['bootmake']; ?>"></td></tr>
            <tr>
                <th>Rental Type:</th>
                <td class="radio-group">
                    <label><input type="radio" name="seasonal" value="1" <?php echo ($rental['seasonal'] == '1') ? 'checked' : ''; ?>> Seasonal</label>
                    <label><input type="radio" name="seasonal" value="0" <?php echo ($rental['seasonal'] == '0') ? 'checked' : ''; ?>> Daily</label>
                </td>
            </tr>
            <tr><th>Salesperson:</th><td><input type="text" name="salesperson" value="<?php echo $rental['salesperson']; ?>"></td></tr>
            <tr><th>Notes:</th><td><input type="text" name="notes" value="<?php echo $rental['notes']; ?>"></td></tr>
        </table>
        <br>
        <input type="submit" value="Update Rental">
    </form>

    <h4><a href="skirentals.php" class="back-link">Back to Rental Page</a></h4>
</div>
</body>
</html>
