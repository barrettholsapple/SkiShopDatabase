<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $boardserialnumber = $_POST['boardserialnumber'];
    $boardmake = $_POST['boardmake'];
    $boardmodel = $_POST['boardmodel'];
    $boardsize = $_POST['boardsize'];
    $boardnotes = $_POST['boardnotes'];

    // Prepare SQL update statement for the boards table
    $query = "UPDATE boards SET boardmake = ?, boardmodel = ?, boardsize = ?, boardnotes = ? WHERE boardserialnumber = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssss", $boardmake, $boardmodel, $boardsize, $boardnotes, $boardserialnumber);

    // Execute and check result
    if ($stmt->execute()) {
        echo "Board updated successfully.";
        header("Location: boardsearch.php"); // Redirect to board list
        exit();
    } else {
        echo "Error updating board: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
