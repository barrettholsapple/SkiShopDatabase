<?php
// Show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get board serial number from URL
$boardserialnumber = $_GET['boardserialnumber'] ?? '';
if (empty($boardserialnumber)) {
    die("No board serial number provided.");
}

// Fetch board info
$board = [];
$query = "SELECT * FROM boards WHERE boardserialnumber = ?";
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
$board = $result->fetch_assoc() ?? [];
$stmt->close();

// Check board availability from boardrentals table
$availabilityQuery = "SELECT COUNT(*) AS rented_count FROM boardrentals WHERE boardserialnumber = ? AND returndate >= CURDATE()";
$availabilityStmt = $conn->prepare($availabilityQuery);
if (!$availabilityStmt) {
    die("Prepare failed (availability): " . $conn->error);
}
$availabilityStmt->bind_param("s", $boardserialnumber);
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
    <title>View or Edit Snowboard Data</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>View Snowboard Information</h1>
    <table>
        <tr><th>Serial Number:</th><td><?php echo htmlspecialchars($board['boardserialnumber'] ?? 'N/A'); ?></td></tr>
        <tr><th>Make and Model:</th><td><?php echo htmlspecialchars(($board['boardmake'] ?? '') . ' ' . ($board['boardmodel'] ?? '')); ?></td></tr>
        <tr><th>Size:</th><td><?php echo htmlspecialchars($board['boardsize'] ?? 'N/A'); ?></td></tr>
        <tr><th>Current Status:</th><td><?php echo $availabilityStatus; ?></td></tr>
        <tr><th>Notes:</th><td><?php echo htmlspecialchars($board['boardnotes'] ?? 'N/A'); ?></td></tr>
    </table>

    <br>
    <h2>Edit Snowboard Information</h2>
    <form method="POST" action="updateboard.php">
        <input type="hidden" name="original_boardserialnumber" value="<?php echo htmlspecialchars($board['boardserialnumber'] ?? ''); ?>">
        <table>
            <tr><th>Serial Number:</th><td><input type="text" name="boardserialnumber" value="<?php echo htmlspecialchars($board['boardserialnumber'] ?? ''); ?>" required></td></tr>
            <tr><th>Make:</th><td><input type="text" name="boardmake" value="<?php echo htmlspecialchars($board['boardmake'] ?? ''); ?>" required></td></tr>
            <tr><th>Model:</th><td><input type="text" name="boardmodel" value="<?php echo htmlspecialchars($board['boardmodel'] ?? ''); ?>" required></td></tr>
            <tr><th>Size:</th><td><input type="text" name="boardsize" value="<?php echo htmlspecialchars($board['boardsize'] ?? ''); ?>" required></td></tr>
            <tr><th>Notes:</th><td><textarea name="boardnotes" rows="4" cols="40"><?php echo htmlspecialchars($board['boardnotes'] ?? ''); ?></textarea></td></tr>
        </table>
        <br>
        <input type="submit" value="Update Snowboard">
    </form>

    <h4><a href="boardsearch.php" class="back-link">Back to Board Search</a></h4>
</div>
</body>
</html>
