<?php
include("inc/db.php");
header('Content-Type: application/json');

$filter = $_GET['filter'] ?? 'today';

$where = "";
switch($filter){
    case 'week':
        $where = "WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $where = "WHERE MONTH(visited_at) = MONTH(CURDATE()) AND YEAR(visited_at) = YEAR(CURDATE())";
        break;
    case 'today':
    default:
        $where = "WHERE DATE(visited_at) = CURDATE()";
        break;
}

// Fetch visitors
$sql = "SELECT country, COUNT(*) AS total
        FROM landing_visitors
        $where
        GROUP BY country
        ORDER BY total DESC";
$result = $conn->query($sql);

$visitors = [];
$total = 0;
while($row = $result->fetch_assoc()){
    $visitors[] = $row;
    $total += $row['total'];
}

echo json_encode(['total' => $total, 'visitors' => $visitors]);
