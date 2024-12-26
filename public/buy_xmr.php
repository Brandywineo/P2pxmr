<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require 'db_connect.php';

// Get amount from query parameter
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$usd_rate = 150; // Example conversion rate: 1 XMR = $150
$usd_amount = $amount * $usd_rate;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount_to_buy = floatval($_POST['amount_to_buy']);
    $total_cost = $amount_to_buy * $usd_rate;

    // Insert transaction into database
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, ad_type, amount, usd_amount) VALUES (?, 'buy', ?, ?)");
    $stmt->bind_param("idd", $_SESSION['user_id'], $amount_to_buy, $total_cost);
    $stmt->execute();

    header("Location: dashboard.php?success=Transaction completed.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Monero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center">Buy Monero</h3>
        <div class="card p-4">
            <form method="post">
                <div class="mb-3">
                    <label for="amount_to_buy" class="form-label">Amount to Buy (XMR):</label>
                    <input type="number" step="0.01" class="form-control" id="amount_to_buy" name="amount_to_buy" value="<?php echo htmlspecialchars($amount); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="usd_cost" class="form-label">Total Cost (USD):</label>
                    <input type="text" class="form-control" id="usd_cost" value="$<?php echo number_format($usd_amount, 2); ?>" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Buy XMR</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
