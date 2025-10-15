<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Search boards by column
function searchBoards($conn, $column, $value) {
    $allowed_columns = ['boardserialnumber', 'boardmodel', 'boardsize'];
    if (!in_array($column, $allowed_columns)) {
        die("Invalid search column.");
    }

    $query = "SELECT * FROM boards WHERE `$column` LIKE ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $searchTerm = "%$value%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all boards
function fetchAllBoards($conn) {
    return $conn->query("SELECT * FROM boards");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snowboard Management</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Snowboard Management</h1>

        <!-- Search Form -->
        <form method="POST" action="">
            <div class="searchdrop">
            <label for="search_type">Search by:</label>
            <select id="search_type" name="search_type">
                <option value="boardserialnumber">Serial Number</option>
                <option value="boardmodel">Model</option>
                <option value="boardsize">Size</option>
            </select> 
            </div>
            <div class="searchbar">
                <input type="text" id="search" name="search" placeholder="Enter search term">
                <button type="submit" name="search_submit">Search</button>
            </div>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_submit'])) {
            $searchTerm = trim($_POST['search']);
            $searchType = $_POST['search_type'];

            if (!empty($searchTerm)) {
                $result = searchBoards($conn, $searchType, $searchTerm);
                $safeSearchTerm = htmlspecialchars($searchTerm);

                echo "<h2>Search Results for '$safeSearchTerm' by $searchType:</h2>";

                if ($result->num_rows > 0) {
                    echo "<table class='ski-table'><tr><th>Serial #</th><th>Make</th><th>Model</th><th>Size</th><th>Notes</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . htmlspecialchars($row['boardserialnumber']) . "</td>
                              <td>" . htmlspecialchars($row['boardmake']) . "</td>
                              <td>" . htmlspecialchars($row['boardmodel']) . "</td>
                              <td>" . htmlspecialchars($row['boardsize']) . "</td>
                              <td>" . htmlspecialchars($row['boardnotes']) . "</td>
                              <td>
                                  <a href='editboard.php?boardserialnumber=" . urlencode($row['boardserialnumber']) . "'>View or Edit</a> |
                                  <a href='deleteboard.php?boardserialnumber=" . urlencode($row['boardserialnumber']) . "' onclick='return confirm(\"Delete " . htmlspecialchars($row['boardmake']) . " " . htmlspecialchars($row['boardmodel']) . "?\");'>Delete</a>
                              </td></tr>";
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

        $resultAllBoards = fetchAllBoards($conn);
        if ($resultAllBoards->num_rows > 0) {
            echo "<h2>All Snowboards:</h2>";
            echo "<table class='ski-table'><tr><th>Serial #</th><th>Make</th><th>Model</th><th>Size</th><th>Notes</th><th>Actions</th></tr>";
            while ($row = $resultAllBoards->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['boardserialnumber']) . "</td>
                      <td>" . htmlspecialchars($row['boardmake']) . "</td>
                      <td>" . htmlspecialchars($row['boardmodel']) . "</td>
                      <td>" . htmlspecialchars($row['boardsize']) . "</td>
                      <td>" . htmlspecialchars($row['boardnotes']) . "</td>
                      <td>
                          <a href='editboard.php?boardserialnumber=" . urlencode($row['boardserialnumber']) . "'>View or Edit</a> |
                          <a href='deleteboard.php?boardserialnumber=" . urlencode($row['boardserialnumber']) . "' onclick='return confirm(\"Delete " . htmlspecialchars($row['boardmake']) . " " . htmlspecialchars($row['boardmodel']) . "?\");'>Delete</a>
                      </td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No snowboards found.</p>";
        }
        ?>

        <h5><a href="addboard.php" class="back-link">Add New Snowboard</a></h3>
        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>