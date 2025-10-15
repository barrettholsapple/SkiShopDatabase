<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get filters
$searchTerm = $_GET['search'] ?? '';
$rentalType = $_GET['rental_type'] ?? '';

// Format search wildcard
$searchWildcard = '%' . $searchTerm . '%';

// Build SQL dynamically
$conditions = [];
$params = [];
$types = "";

// For SKIS
$skiQuery = "
    SELECT 
        c.customerid, 
        c.fname AS FirstName,
        c.lname AS LastName,
        s.serialnumber AS SerialNumber,
        s.make AS Make,
        s.model AS Model,
        pr.daterented AS DateRented,
        pr.datereturned AS DateReturned,
        'ski' AS EquipmentType
    FROM pastrentals pr
    JOIN customers c ON pr.customerid = c.customerid
    JOIN skis s ON pr.serialnumber = s.serialnumber
";

// For BOARDS
$boardQuery = "
    SELECT 
        c.customerid, 
        c.fname AS FirstName,
        c.lname AS LastName,
        b.boardserialnumber AS SerialNumber,
        b.boardmake AS Make,
        b.boardmodel AS Model,
        pbr.daterented AS DateRented,
        pbr.datereturned AS DateReturned,
        'board' AS EquipmentType
    FROM pastboardrentals pbr
    JOIN customers c ON pbr.customerid = c.customerid
    JOIN boards b ON pbr.boardserialnumber = b.boardserialnumber
";

// Apply rental type filter
switch ($rentalType) {
    case 'skis':
        $mainQuery = $skiQuery;
        if (!empty($searchTerm)) {
            $mainQuery .= " WHERE (s.serialnumber LIKE ? OR c.lname LIKE ?)";
            $params = [$searchWildcard, $searchWildcard];
            $types = "ss";
        }
        break;
    case 'snowboards':
        $mainQuery = $boardQuery;
        if (!empty($searchTerm)) {
            $mainQuery .= " WHERE (b.boardserialnumber LIKE ? OR c.lname LIKE ?)";
            $params = [$searchWildcard, $searchWildcard];
            $types = "ss";
        }
        break;
    default:
        // All Rentals (UNION)
        $mainQuery = $skiQuery . " UNION " . $boardQuery;
        if (!empty($searchTerm)) {
            $mainQuery = "
                SELECT * FROM (
                    $mainQuery
                ) AS combined
                WHERE (SerialNumber LIKE ? OR LastName LIKE ?)
            ";
            $params = [$searchWildcard, $searchWildcard];
            $types = "ss";
        }
        break;
}

// Prepare and execute
$stmt = $conn->prepare($mainQuery);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Rentals</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
<div class="container">
    <h1>Past Rentals</h1>

    <!-- Search bar -->
    <form method="GET" class="searchbar">
        <label for="search">Search by Serial Number or Last Name:</label>
        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Enter search term">
        <button type="submit">Search</button>
    </form>

    <!-- Filter -->
    <form method="GET" class="rental-filter">
        <label for="rental_type">Filter by:</label>
        <select id="rental_type" name="rental_type">
            <option value="">All Rentals</option>
            <option value="skis" <?php echo ($rentalType == 'skis') ? 'selected' : ''; ?>>Skis</option>
            <option value="snowboards" <?php echo ($rentalType == 'snowboards') ? 'selected' : ''; ?>>Snowboards</option>
        </select>
        <button type="submit">Apply Filter</button>
    </form>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr><th>Customer Name</th><th>Equipment</th><th>Rental Date</th><th>Return Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $customerLink = "editcustomer.php?id={$row['customerid']}";
            $equipmentLink = ($row['EquipmentType'] === 'ski') 
                ? "editski.php?serialnumber={$row['SerialNumber']}" 
                : "editboard.php?boardserialnumber={$row['SerialNumber']}";

            echo "<tr>
                    <td><a href='$customerLink'>{$row['LastName']}, {$row['FirstName']}</a></td>
                    <td><a href='$equipmentLink'>{$row['SerialNumber']} {$row['Make']} {$row['Model']}</a></td>
                    <td>{$row['DateRented']}</td>
                    <td>{$row['DateReturned']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }
    ?>

<h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
</div>
</body>
</html>
<?php
$conn->close();
?>
