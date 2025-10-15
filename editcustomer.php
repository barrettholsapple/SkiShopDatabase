<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure an ID is provided in the URL
if (!isset($_GET['id'])) {
    die("Error: No customer ID provided.");
}

$customerid = $_GET['id'];
$query = "SELECT * FROM customers WHERE customerid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customerid);

$customer = [];
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc() ?? [];
} else {
    die("Error executing query: " . $stmt->error);
}

// Close statement
$stmt->close();

// Calculate age from birthdate
if (isset($customer['birthday'])) {
    $birthday = new DateTime($customer['birthday']);
    $today = new DateTime('today');
    $age = $birthday->diff($today)->y;
} else {
    $age = "N/A";
}

// Format phone number
$phonenum = isset($customer['phonenum']) ? '(' . substr($customer['phonenum'], 0, 3) . ')-' . substr($customer['phonenum'], 3, 3) . '-' . substr($customer['phonenum'], 6) : 'N/A';

// Skill Level mapping
$skillLevels = [1 => '1', 2 => 'Intermediate', 3 => 'Advanced'];
$skillLevel = isset($customer['skilllevel']) ? $skillLevels[$customer['skilllevel']] : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View or Edit Customer Data</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>View Customer Information</h1>
        <table>
            <tr><th>Full Name:</th><td><?php echo isset($customer['fname'], $customer['lname']) ? $customer['fname'] . ' ' . $customer['lname'] : 'N/A'; ?></td></tr>
            <tr><th>Address:</th><td><?php echo isset($customer['streetadd'], $customer['city'], $customer['stateadd'], $customer['zip']) ? $customer['streetadd'] . ' ' . $customer['city'] . ', ' . $customer['stateadd'] . ' ' . $customer['zip'] : 'N/A'; ?></td></tr>
            <tr><th>Phone Number:</th><td><?php echo $phonenum; ?></td></tr>
            <tr><th>License #:</th><td><?php echo isset($customer['driversli']) ? $customer['driversli'] : 'N/A'; ?></td></tr>
            <tr><th>Email:</th><td><?php echo isset($customer['email']) ? $customer['email'] : 'N/A'; ?></td></tr>
            <tr><th>Age:</th><td><?php echo $age; ?></td></tr>
            <tr><th>Skill Level:</th><td><?php echo $skillLevel; ?></td></tr>
            <tr><th>Weight:</th><td><?php echo isset($customer['weightlbs']) ? $customer['weightlbs'] . ' lbs' : 'N/A'; ?></td></tr>
            <tr><th>Height:</th><td><?php echo isset($customer['heightft'], $customer['heightin']) ? $customer['heightft'] . "' " . $customer['heightin'] . '"' : 'N/A'; ?></td></tr>
        </table>
        <br>
        <h2>Edit Customer Information</h2>
        <form method="POST" action="updatecustomer.php">
            <input type="hidden" name="customerid" value="<?php echo isset($customer['customerid']) ? $customer['customerid'] : ''; ?>" />
            <table>
                <tr><th>First Name:</th><td><input type="text" name="fname" value="<?php echo htmlspecialchars($customer['fname']); ?>" /></td></tr>
                <tr><th>Last Name:</th><td><input type="text" name="lname" value="<?php echo isset($customer['lname']) ? $customer['lname'] : ''; ?>" /></td></tr>
                <tr><th>Street:</th><td><input type="text" name="streetadd" value="<?php echo isset($customer['streetadd']) ? $customer['streetadd'] : ''; ?>" /></td></tr>
                <tr><th>City:</th><td><input type="text" name="city" value="<?php echo isset($customer['city']) ? $customer['city'] : ''; ?>" /></td></tr>
                <tr><th>State:</th><td><input type="text" name="stateadd" value="<?php echo isset($customer['stateadd']) ? $customer['stateadd'] : ''; ?>" /></td></tr>
                <tr><th>Zip Code:</th><td><input type="number" name="zip" value="<?php echo isset($customer['zip']) ? $customer['zip'] : ''; ?>" /></td></tr>
                <tr><th>Driver's License #:</th><td><input type="text" name="driversli" value="<?php echo isset($customer['driversli']) ? $customer['driversli'] : ''; ?>" /></td></tr>
                <tr><th>Phone Number:</th><td><input type="text" name="phonenum" value="<?php echo isset($customer['phonenum']) ? $customer['phonenum'] : ''; ?>" /></td></tr>
                <tr><th>Email:</th><td><input type="text" name="email" value="<?php echo isset($customer['email']) ? $customer['email'] : ''; ?>" /></td></tr>
                <tr><th>Birthday:</th><td><input type="date" name="birthday" value="<?php echo isset($customer['birthday']) ? $customer['birthday'] : ''; ?>" /></td></tr>
                <tr><th>Weight:</th><td><input type="number" name="weightlbs" value="<?php echo isset($customer['weightlbs']) ? $customer['weightlbs'] : ''; ?>" /></td></tr>
                <tr><th>Height:</th><td>
                    <input type="number" name="heightft" style="width: 100px;" value="<?php echo isset($customer['heightft']) ? $customer['heightft'] : ''; ?>" /> ' 
                    <input type="number" name="heightin" style="width: 100px;" value="<?php echo isset($customer['heightin']) ? $customer['heightin'] : ''; ?>" /> "
                </td></tr>
                <tr>
                <th>Skill Level:</th>
                <td>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="skilllevel" value="1" 
                                <?php echo (isset($customer['skilllevel']) && $customer['skilllevel'] == 1) ? 'checked' : ''; ?>>
                            Beginner
                        </label>
                        <label>
                            <input type="radio" name="skilllevel" value="2" 
                                <?php echo (isset($customer['skilllevel']) && $customer['skilllevel'] == 2) ? 'checked' : ''; ?>>
                            Intermediate
                        </label>
                        <label>
                            <input type="radio" name="skilllevel" value="3" 
                                <?php echo (isset($customer['skilllevel']) && $customer['skilllevel'] == 3) ? 'checked' : ''; ?>>
                            Advanced
                        </label>
                    </div>
                </td>
            </tr>

            </table>
            <br>
            <input type="submit" value="Update Customer" />
        </form>
        <h4><a href="customers.php" style="color:rgb(112, 216, 250); text-decoration: none; font-size: 18px;" onmouseover="this.style.color='#fff'; this.style.backgroundColor='#333'; this.style.textDecoration='underline';" onmouseout="this.style.color='#f5b380'; this.style.backgroundColor=''; this.style.textDecoration='none';">Back to Customer Search</a></h4>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
