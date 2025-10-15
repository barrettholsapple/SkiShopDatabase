<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to search customers by last name or phone number
function searchCustomers($conn, $value) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE lname LIKE ? OR phonenum LIKE ?");
    $searchTerm = "%$value%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all customers
function fetchAllCustomers($conn) {
    $query = "SELECT * FROM customers ORDER BY lname ASC";
    return $conn->query($query);
}

// Function to format phone number as (xxx)-xxx-xxxx
function formatPhoneNumber($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);
    
    // Check if phone number is valid and format it
    if (strlen($phone) == 10) {
        // Format as (xxx)-xxx-xxxx
        return '(' . substr($phone, 0, 3) . ')-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
    }
    // If it's not valid, return the original number (or you can return an empty string or error message)
    return $phone;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Management</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Customer Management</h1>

        <!-- Search Form -->
        <form method="POST" action="">
            <label for="search">Search by Last Name or Phone Number:</label>
            <div class="searchbar">
                <input type="text" id="search" name="search" placeholder="Enter search term">
                <button type="submit" name="search_submit">Search</button>
            </div>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_submit'])) {
            $searchTerm = trim($_POST['search']);

            if (!empty($searchTerm)) {
                $result = searchCustomers($conn, $searchTerm);
                $safeSearchTerm = htmlspecialchars($searchTerm);

                echo "<h2>Search Results for '$safeSearchTerm':</h2>";

                if ($result->num_rows > 0) {
                    echo "<table class='customer-table'><tr><th>Last Name</th><th>First Name</th><th>Phone Number</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        // Format the phone number for display
                        $formattedPhone = formatPhoneNumber($row['phonenum']);
                        echo "<tr><td>" . htmlspecialchars($row['lname']) . "</td><td>" . htmlspecialchars($row['fname']) . "</td><td>" . $formattedPhone . "</td>";
                        echo "<td><a href='editcustomer.php?id=" . $row['customerid'] . "'>View or Edit</a> | 
                              <a href='deletecustomer.php?id=" . $row['customerid'] . "' onclick='return confirm(\"Are you sure you want to delete customer " . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']) . "?\");'>Delete</a></td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No results found.</p>";
                }
            } else {
                echo "<p>Please enter a search term.</p>";
            }
        }

        echo "<br>";

        $resultAllCustomers = fetchAllCustomers($conn);
        if ($resultAllCustomers->num_rows > 0) {
            echo "<h2>All Customers:</h2>";
            echo "<table class='customer-table'><tr><th>Last Name</th><th>First Name</th><th>Phone Number</th><th>Actions</th></tr>";
            while ($row = $resultAllCustomers->fetch_assoc()) {
                // Format the phone number for display
                $formattedPhone = formatPhoneNumber($row['phonenum']);
                echo "<tr><td>" . htmlspecialchars($row['lname']) . "</td><td>" . htmlspecialchars($row['fname']) . "</td><td>" . $formattedPhone . "</td>";
                echo "<td><a href='editcustomer.php?id=" . $row['customerid'] . "'>View or Edit</a> | 
                      <a href='deletecustomer.php?id=" . $row['customerid'] . "' onclick='return confirm(\"Are you sure you want to delete customer " . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']) . "?\");'>Delete</a></td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No customers found.</p>";
        }
        ?>

        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>
