<?php
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$term = $_GET['term'] ?? '';

$sql = "
    SELECT serialnumber, make, model, sizeof 
    FROM skis 
    WHERE serialnumber NOT IN (SELECT serialnumber FROM rentals)
    AND (make LIKE ? OR model LIKE ? OR serialnumber LIKE ?)
    LIMIT 10
";

$stmt = $conn->prepare($sql);
$searchTerm = '%' . $term . '%';
$stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['serialnumber'],
        'label' => "{$row['serialnumber']} - {$row['make']} {$row['model']} ({$row['sizeof']})"
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
