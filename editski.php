<?php
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$serialnumber = $_GET['serialnumber'] ?? '';
if (empty($serialnumber)) {
    die("No serial number provided.");
}

$ski = [];
if (!empty($serialnumber)) {
    $query = "SELECT * FROM skis WHERE serialnumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $serialnumber);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $ski = $result->fetch_assoc() ?? [];
    } else {
        die("Error executing query: " . $stmt->error);
    }
    $stmt->close();
}

$availabilityQuery = "SELECT COUNT(*) AS rented_count FROM rentals WHERE serialnumber = ? AND returndate >= CURDATE()";
$availabilityStmt = $conn->prepare($availabilityQuery);
$availabilityStmt->bind_param("s", $serialnumber);
$availabilityStmt->execute();
$availabilityResult = $availabilityStmt->get_result();
$availabilityData = $availabilityResult->fetch_assoc();
$availabilityStatus = $availabilityData['rented_count'] > 0 ? 'Out for Rental' : 'Available';

$availabilityStmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View or Edit Ski Data</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>View Ski Information</h1>
    <table>
        <tr><th>Serial Number:</th><td><?php echo htmlspecialchars($ski['serialnumber'] ?? 'N/A'); ?></td></tr>
        <tr><th>Make and Model:</th><td><?php echo htmlspecialchars(($ski['make'] ?? '') . ' ' . ($ski['model'] ?? '')); ?></td></tr>
        <tr><th>Size:</th><td><?php echo htmlspecialchars($ski['sizeof'] ?? 'N/A'); ?></td></tr>
        <tr><th>Current Status:</th><td><?php echo $availabilityStatus; ?></td></tr>
        <tr><th>Notes:</th><td><?php echo htmlspecialchars($ski['notes'] ?? 'N/A'); ?></td></tr>
    </table>

    <br>
    <h2>Edit Ski Information</h2>
    <form method="POST" action="updateski.php">
        <input type="hidden" name="original_serialnumber" value="<?php echo htmlspecialchars($ski['serialnumber'] ?? ''); ?>">
        <table>
            <tr><th>Serial Number:</th><td><input type="text" name="serialnumber" value="<?php echo htmlspecialchars($ski['serialnumber'] ?? ''); ?>" required></td></tr>
            <tr><th>Make:</th><td><input type="text" name="make" value="<?php echo htmlspecialchars($ski['make'] ?? ''); ?>" required></td></tr>
            <tr><th>Model:</th><td><input type="text" name="model" value="<?php echo htmlspecialchars($ski['model'] ?? ''); ?>" required></td></tr>
            <tr><th>Size:</th><td><input type="text" name="sizeof" value="<?php echo htmlspecialchars($ski['sizeof'] ?? ''); ?>"></td></tr>
            <tr><th>Notes:</th><td><textarea name="notes" rows="4" cols="40"><?php echo htmlspecialchars($ski['notes'] ?? ''); ?></textarea></td></tr>
        </table>
        <br>
        <input type="submit" value="Update Ski">
    </form>

    <h4><a href="skisearch.php" class="back-link">Back to Ski Search</a></h4>
</div>
</body>
</html>

