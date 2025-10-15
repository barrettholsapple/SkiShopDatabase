<?php
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) die("DB connection failed");

$term = $_GET['term'] ?? '';
$stmt = $conn->prepare("SELECT customerid, fname, lname FROM customers WHERE lname LIKE CONCAT(?, '%') OR fname LIKE CONCAT(?, '%') ORDER BY lname LIMIT 10");
$stmt->bind_param("ss", $term, $term);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        'id' => $row['customerid'],
        'label' => "{$row['lname']}, {$row['fname']}"
    ];
}
echo json_encode($results);
?>
