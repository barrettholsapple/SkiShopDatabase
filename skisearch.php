<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Search skis by column
function searchSkis($conn, $column, $value) {
    $allowed_columns = ['serialnumber', 'model', 'sizeof'];
    if (!in_array($column, $allowed_columns)) {
        die("Invalid search column.");
    }

    $query = "SELECT * FROM skis WHERE `$column` LIKE ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $searchTerm = "%$value%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all skis
function fetchAllSkis($conn) {
    return $conn->query("SELECT * FROM skis");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ski Management</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Ski Management</h1>

        <!-- Search Form -->
        <form method="POST" action="">
            <div class="searchdrop">
            <label for="search_type">Search by:</label>
            <select id="search_type" name="search_type">
                <option value="serialnumber">Serial Number</option>
                <option value="model">Model</option>
                <option value="sizeof">Size</option>
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
                $result = searchSkis($conn, $searchType, $searchTerm);
                $safeSearchTerm = htmlspecialchars($searchTerm);

                echo "<h2>Search Results for '$safeSearchTerm' by $searchType:</h2>";

                if ($result->num_rows > 0) {
                    echo "<table class='ski-table'><tr><th>Serial #</th><th>Make</th><th>Model</th><th>Size</th><th>Notes</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . htmlspecialchars($row['serialnumber']) . "</td>
                              <td>" . htmlspecialchars($row['make']) . "</td>
                              <td>" . htmlspecialchars($row['model']) . "</td>
                              <td>" . htmlspecialchars($row['sizeof']) . "</td>
                              <td>" . htmlspecialchars($row['notes']) . "</td>
                              <td>
                                  <a href='editski.php?serialnumber=" . urlencode($row['serialnumber']) . "'>View or Edit</a> |
                                  <a href='deleteski.php?serialnumber=" . urlencode($row['serialnumber']) . "' onclick='return confirm(\"Delete " . htmlspecialchars($row['make']) . " " . htmlspecialchars($row['model']) . "?\");'>Delete</a>
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

        $resultAllSkis = fetchAllSkis($conn);
        if ($resultAllSkis->num_rows > 0) {
            echo "<h2>All Skis:</h2>";
            echo "<table class='ski-table'><tr><th>Serial #</th><th>Make</th><th>Model</th><th>Size</th><th>Notes</th><th>Actions</th></tr>";
            while ($row = $resultAllSkis->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['serialnumber']) . "</td>
                      <td>" . htmlspecialchars($row['make']) . "</td>
                      <td>" . htmlspecialchars($row['model']) . "</td>
                      <td>" . htmlspecialchars($row['sizeof']) . "</td>
                      <td>" . htmlspecialchars($row['notes']) . "</td>
                      <td>
                          <a href='editski.php?serialnumber=" . urlencode($row['serialnumber']) . "'>View or Edit</a> |
                          <a href='deleteski.php?serialnumber=" . urlencode($row['serialnumber']) . "' onclick='return confirm(\"Delete " . htmlspecialchars($row['make']) . " " . htmlspecialchars($row['model']) . "?\");'>Delete</a>
                      </td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No skis found.</p>";
        }
        ?>

        <h3><a href="addski.php" class="back-link">Add New Ski</a></h3>
        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>

