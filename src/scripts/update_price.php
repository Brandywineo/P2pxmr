<?php
require '../src/config/db.php';

$api_url = 'https://api.coingecko.com/api/v3/simple/price?ids=monero&vs_currencies=usd';
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if (isset($data['monero']['usd'])) {
    $price_usd = $data['monero']['usd'];
    $stmt = $conn->prepare("INSERT INTO xmr_prices (price_usd) VALUES (?)");
    $stmt->bind_param("d", $price_usd);
    $stmt->execute();
    echo "Price updated: $price_usd";
} else {
    echo "Failed to fetch price.";
}
?>
