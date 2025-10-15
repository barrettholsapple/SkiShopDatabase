<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $serialnumber = $_POST['serialnumber'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $sizeof = $_POST['sizeof'];
    $notes = $_POST['notes'];

    // Prepare SQL update statement for the skis table
    $query = "UPDATE skis SET make = ?, model = ?, sizeof = ?, notes = ? WHERE serialnumber = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssss", $make, $model, $sizeof, $notes, $serialnumber);

    // Execute and check result
    if ($stmt->execute()) {
        echo "Ski updated successfully.";
        header("Location: skisearch.php"); // Redirect to ski list
        exit();
    } else {
        echo "Error updating ski: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>

