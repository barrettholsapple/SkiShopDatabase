<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Function to search rentals
function searchRentals($conn, $column, $value) {
    $validColumns = ['lname', 'boardserialnumber', 'phonenum'];
    if (!in_array($column, $validColumns)) {
        die("Invalid column for search.");
    }

    if ($column == 'lname' || $column == 'phonenum') {
        $query = "SELECT boardrentals.*, customers.fname, customers.lname, customers.phonenum, boards.boardmake, boards.boardmodel
                  FROM boardrentals
                  JOIN customers ON boardrentals.customerid = customers.customerid
                  JOIN boards ON boardrentals.boardserialnumber = boards.boardserialnumber
                  WHERE customers.$column LIKE ?";
    } else {
        $query = "SELECT boardrentals.*, customers.fname, customers.lname, customers.phonenum, boards.boardmake, boards.boardmodel
                  FROM boardrentals
                  JOIN customers ON boardrentals.customerid = customers.customerid
                  JOIN boards ON boardrentals.boardserialnumber = boards.boardserialnumber
                  WHERE boardrentals.$column LIKE ?";
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $searchTerm = "%$value%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all board rentals
function fetchAllBoardRentals($conn) {
    $query = "SELECT boardrentals.*, customers.fname, customers.lname, customers.phonenum, boards.boardmake, boards.boardmodel
              FROM boardrentals
              JOIN customers ON boardrentals.customerid = customers.customerid
              JOIN boards ON boardrentals.boardserialnumber = boards.boardserialnumber
              ORDER BY boardrentals.returndate DESC";
    return $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Board Rental Management</title>
    <link rel="stylesheet" href="skishopstyles.css"> <!-- Update path if needed -->
</head>
<body>
    <div class="container">
        <h1>Snowboard Rental Management</h1>

        <!-- Search Form -->
        <form method="POST" action="">
            <div class="searchdrop">
            <label for="search_type">Search by:</label>
            <select id="search_type" name="search_type">
                <option value="boardserialnumber">Serial Number</option>
                <option value="lname">Last Name</option>
                <option value="phonenum">Phone Number</option>
            </select>
            </div>
            <div class="searchbar">
                <input type="text" id="search" name="search" placeholder="Enter search term" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                <button type="submit" name="search_submit">Search</button>
            </div>
        </form>

        <?php
        $searchTerm = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_submit'])) {
            $searchTerm = trim($_POST['search']);
            $searchType = $_POST['search_type'];

            if (!empty($searchTerm)) {
                $result = searchRentals($conn, $searchType, $searchTerm);

                echo "<h2>Search Results for '$searchTerm' by $searchType:</h2>";

                if ($result->num_rows > 0) {
                    echo "<table><tr><th>Last Name</th><th>First Name</th><th>Serial Number</th><th>Make & Model</th><th>Rental Date</th><th>Return Date</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['lname']}</td>
                                <td>{$row['fname']}</td>
                                <td>{$row['boardserialnumber']}</td>
                                <td>{$row['boardmake']} {$row['boardmodel']}</td>
                                <td>{$row['daterented']}</td>
                                <td>{$row['returndate']}</td>
                                <td>
                                    <a href='editboardrental.php?boardserialnumber={$row['boardserialnumber']}'>View and Edit</a> | 
                                    <a href='deleteboardrental.php?boardserialnumber={$row['boardserialnumber']}' onclick='return confirm(\"Are you sure?\");'>Delete</a> | 
                                    <a href='returnboard.php?customerid={$row['customerid']}'>Return</a>
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

            echo "<br>";
        }

        // Display all rentals
        $resultAll = fetchAllBoardRentals($conn);
        if ($resultAll->num_rows > 0) {
            echo "<h2>All Rentals:</h2>";
            echo "<table><tr><th>Last Name</th><th>First Name</th><th>Serial Number</th><th>Make & Model</th><th>Rental Date</th><th>Return Date</th><th>Actions</th></tr>";
            while ($row = $resultAll->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['lname']}</td>
                        <td>{$row['fname']}</td>
                        <td>{$row['boardserialnumber']}</td>
                        <td>{$row['boardmake']} {$row['boardmodel']}</td>
                        <td>{$row['daterented']}</td>
                        <td>{$row['returndate']}</td>
                        <td>
                            <a href='editboardrental.php?boardserialnumber={$row['boardserialnumber']}'>View and Edit</a> | 
                            <a href='deleteboardrental.php?boardserialnumber={$row['boardserialnumber']}' onclick='return confirm(\"Are you sure?\");'>Delete</a> | 
                            <a href='returnboard.php?customerid={$row['customerid']}'>Return</a>
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
