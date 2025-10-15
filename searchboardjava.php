<?php
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$term = $_GET['term'] ?? '';

$stmt = $conn->prepare("
    SELECT boardserialnumber, boardmake, boardmodel, boardsize
    FROM boards
    WHERE CONCAT(boardserialnumber, ' ', boardmake, ' ', boardmodel, ' ', boardsize) LIKE CONCAT('%', ?, '%')
    AND boardserialnumber NOT IN (SELECT boardserialnumber FROM boardrentals)
    LIMIT 10
");
$stmt->bind_param('s', $term);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        'id' => $row['boardserialnumber'],
        'label' => "{$row['boardserialnumber']} - {$row['boardmake']} {$row['boardmodel']} ({$row['boardsize']})"
    ];
}

header('Content-Type: application/json');
echo json_encode($results);
?>
