<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../src/config/db.php';

// Get amount from query parameter
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$usd_rate = 150; // Example conversion rate: 1 XMR = $150
$usd_amount = $amount * $usd_rate;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount_to_sell = floatval($_POST['amount_to_sell']);
    $total_earnings = $amount_to_sell * $usd_rate;

    // Insert transaction into database
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, ad_type, amount, usd_amount) VALUES (:user_id, 'sell', :amount, :usd_amount)");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':amount' => $amount_to_sell,
        ':usd_amount' => $total_earnings
    ]);

    header("Location: dashboard.php?success=Transaction completed.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Monero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center">Sell Monero</h3>
        <div class="card p-4">
            <form method="post">
                <div class="mb-3">
                    <label for="amount_to_sell" class="form-label">Amount to Sell (XMR):</label>
                    <input type="number" step="0.01" class="form-control" id="amount_to_sell" name="amount_to_sell" value="<?php echo htmlspecialchars($amount); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="usd_earnings" class="form-label">Total Earnings (USD):</label>
                    <input type="text" class="form-control" id="usd_earnings" value="$<?php echo number_format($usd_amount, 2); ?>" readonly>
                </div>
                <button type="submit" class="btn btn-success">Sell XMR</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
