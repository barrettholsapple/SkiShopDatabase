<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Function to search rentals based on input field (Last Name, Phone Number, Serial Number)
function searchRentals($conn, $column, $value) {
    $validColumns = ['lname', 'serialnumber', 'phonenum'];

    // Check if the provided column is valid
    if (!in_array($column, $validColumns)) {
        die("Invalid column for search.");
    }

    // Build the query with the valid column name
    if ($column == 'lname' || $column == 'phonenum') {
        // Use `customers.lname` or `customers.phonenum` for search
        $query = "SELECT rentals.*, customers.fname, customers.lname, customers.phonenum, skis.make, skis.model, rentals.returndate 
                  FROM rentals
                  JOIN customers ON rentals.customerid = customers.customerid
                  JOIN skis ON rentals.serialnumber = skis.serialnumber
                  WHERE customers.$column LIKE ?";
    } else {
        // Use `rentals.serialnumber` for serial number search
        $query = "SELECT rentals.*, customers.fname, customers.lname, customers.phonenum, skis.make, skis.model, rentals.returndate 
                  FROM rentals
                  JOIN customers ON rentals.customerid = customers.customerid
                  JOIN skis ON rentals.serialnumber = skis.serialnumber
                  WHERE rentals.$column LIKE ?";
    }

    // Prepare the query
    $stmt = $conn->prepare($query);
    
    // Check if prepare() succeeded
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error); // Show the actual error
    }

    $searchTerm = "%$value%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all rentals (for displaying when no search is performed)
function fetchAllRentals($conn) {
    $query = "SELECT rentals.*, customers.fname, customers.lname, customers.phonenum, skis.make, skis.model, rentals.returndate
              FROM rentals
              JOIN customers ON rentals.customerid = customers.customerid
              JOIN skis ON rentals.serialnumber = skis.serialnumber
              ORDER BY rentals.returndate DESC";
    $result = $conn->query($query);
    return $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rental Management</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Rental Management</h1>

        <!-- Search Form -->
        <form method="POST" action="">
            <div class="searchdrop">
            <label for="search_type">Search by:</label>
            <select id="search_type" name="search_type">
                <option value="serialnumber">Serial Number</option>
                <option value="lname">Last Name</option>
                <option value="phonenum">Phone Number</option>
            </select> 
            </div>
            <div class="searchbar">
                <input type="text" id="search" name="search" placeholder="Enter search term" value="<?php echo isset($searchTerm) ? htmlspecialchars($searchTerm) : ''; ?>">
                <button type="submit" name="search_submit">Search</button>
            </div>
        </form>

        <?php
        // Initialize the search term variable
        $searchTerm = '';

        // Handle search request
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_submit'])) {
            $searchTerm = trim($_POST['search']);
            $searchType = $_POST['search_type'];

            if (!empty($searchTerm)) {
                $result = searchRentals($conn, $searchType, $searchTerm);

                // Display search results with the searched term
                echo "<h2>Search Results for '$searchTerm' by $searchType:</h2>";

                if ($result->num_rows > 0) {
                    echo "<table><tr><th>Last Name</th><th>First Name</th><th>Serial Number</th><th>Board Make and Model</th><th>Rental Date</th><th>Return Date</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['lname'] . "</td>
                                <td>" . $row['fname'] . "</td>
                                <td>" . $row['serialnumber'] . "</td>
                                <td>" . $row['make'] . " " . $row['model'] . "</td>
                                <td>" . $row['daterented'] . "</td>
                                <td>" . $row['returndate'] . "</td>
                                <td>
                                    <a href='editskirental.php?serialnumber=" . $row['serialnumber'] . "'>View and Edit</a> | 
                                    <a href='deleteskirental.php?serialnumber=" . $row['serialnumber'] . "' onclick='return confirm(\"Are you sure?\");'>Delete</a> | 
                                    <a href='returnrentalski.php?customerid=" . $row['customerid'] . "'>Return</a> 
                                </td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No results found.</p>";
                }
            } else {
                echo "<p>Please enter a search term.</p>";
            }
        }

        // Add a line break between search results and all rentals section
        echo "<br>"; // Add a line break

        // Display all rentals below search results if no search has been made or after displaying search results
        $resultAllRentals = fetchAllRentals($conn);
        if ($resultAllRentals->num_rows > 0) {
            echo "<h2>All Rentals:</h2>";
            echo "<table><tr><th>Last Name</th><th>First Name</th><th>Serial Number</th><th>Board Make and Model</th><th>Rental Date</th><th>Return Date</th><th>Actions</th></tr>";
            while ($row = $resultAllRentals->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['lname'] . "</td>
                        <td>" . $row['fname'] . "</td>
                        <td>" . $row['serialnumber'] . "</td>
                        <td>" . $row['make'] . " " . $row['model'] . "</td>
                        <td>" . $row['daterented'] . "</td>
                        <td>" . $row['returndate'] . "</td>
                        <td>
                            <a href='editskirental.php?serialnumber=" . $row['serialnumber'] . "'>View and Edit</a> | 
                            <a href='deleteskirental.php?serialnumber=" . $row['serialnumber'] . "' onclick='return confirm(\"Are you sure?\");'>Delete</a> | 
                            <a href='returnrentalski.php?customerid=" . $row['customerid'] . "'>Return</a> 
                        </td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No active rentals found.</p>";
        }
        ?>

<h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>    
</div>
</body>
</html>

<?php
$conn->close();
?>
