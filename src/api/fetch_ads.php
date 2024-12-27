<?php
// Include database connection
require '../config/db.php';

// Default Response
header('Content-Type: application/json');
$response = [];

// Retrieve Query Parameters
$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'cheapest'; // Default sort is 'cheapest'

// Sorting Logic
$orderBy = 'price ASC';
if ($sort === 'expensive') {
    $orderBy = 'price DESC';
}

// Fetch Ads with Pagination (Infinite Scroll)
$query = $pdo->prepare("
    SELECT id, ad_type, price, 
           ROUND(((price - (SELECT price FROM latest_price LIMIT 1)) / (SELECT price FROM latest_price LIMIT 1)) * 100, 2) AS percentage
    FROM ads
    WHERE id > ? 
    ORDER BY $orderBy
    LIMIT 10
");
$query->execute([$last_id]);

// Fetch Results
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $response[] = $row;
}

// Return JSON Response
echo json_encode($response);
exit;
