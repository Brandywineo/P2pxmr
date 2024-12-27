<?php
require '../src/config/db.php';

$result = $conn->query("SELECT price_usd FROM xmr_prices ORDER BY updated_at DESC LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['price_usd' => $row['price_usd']]);
} else {
    echo json_encode(['price_usd' => 'N/A']);
}
?>
