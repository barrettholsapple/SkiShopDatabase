<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $customerid = $_POST['customerid'];
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $streetadd = trim($_POST['streetadd']);
    $city = trim($_POST['city']);
    $stateadd = trim($_POST['stateadd']);
    $zip = trim($_POST['zip']);
    $driversli = trim($_POST['driversli']);
    $phonenum = trim($_POST['phonenum']);
    $email = trim($_POST['email']);
    $birthday = $_POST['birthday'];
    $weightlbs = $_POST['weightlbs'];
    $heightft = $_POST['heightft'];
    $heightin = $_POST['heightin'];
    $skilllevel = $_POST['skilllevel'];

    // Prepare SQL update statement
    $query = "UPDATE customers SET 
                fname = ?, lname = ?, streetadd = ?, city = ?, stateadd = ?, zip = ?, 
                driversli = ?, phonenum = ?, email = ?, birthday = ?, 
                weightlbs = ?, heightft = ?, heightin = ?, skilllevel = ? 
              WHERE customerid = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ssssssssssiiiii", 
        $fname, $lname, $streetadd, $city, $stateadd, $zip, 
        $driversli, $phonenum, $email, $birthday, 
        $weightlbs, $heightft, $heightin, $skilllevel, $customerid
    );

    if ($stmt->execute()) {
        header("Location: customers.php");
        exit(); // Important to prevent further execution
    } else {
        echo "Error updating customer: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>