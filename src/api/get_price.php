<?php
// Include database connection
require '../src/config/db.php'; // Adjust path as needed

header('Content-Type: application/json');

try {
    // API endpoint
    $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=monero&vs_currencies=usd";

    // Fetch the response from CoinGecko
    $response = file_get_contents($apiUrl);

    if ($response !== false) {
        $data = json_decode($response, true);

        if (isset($data['monero']['usd'])) {
            $price = $data['monero']['usd'];

            // Check if the price already exists in the database
            $stmt = $pdo->prepare("SELECT * FROM crypto_prices WHERE currency = 'monero'");
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update the existing price
                $updateStmt = $pdo->prepare("UPDATE crypto_prices SET price = ?, updated_at = NOW() WHERE currency = 'monero'");
                $updateStmt->execute([$price]);
            } else {
                // Insert the new price
                $insertStmt = $pdo->prepare("INSERT INTO crypto_prices (currency, price) VALUES ('monero', ?)");
                $insertStmt->execute([$price]);
            }

            // Output the latest price
            echo json_encode(['success' => true, 'price' => $price]);
        } else {
            echo json_encode(['error' => 'Invalid response format from CoinGecko.']);
        }
    } else {
        echo json_encode(['error' => 'Unable to fetch price data from CoinGecko.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
