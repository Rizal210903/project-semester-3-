<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "tk_pertiwi_db");
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$result = $conn->query("SELECT id, title, description, start_date, end_date, category FROM events");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'start' => $row['start_date'],
        'end' => $row['end_date'] ? $row['end_date'] : $row['start_date'],
        'extendedProps' => ['category' => $row['category']]
    ];
}
echo json_encode($events);
$conn->close();
?>