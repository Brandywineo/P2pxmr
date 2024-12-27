<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require '../src/config/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $ad_type = $_POST['ad_type'];
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $payment_method = $_POST['payment_method'];

    // Convert amount to Monero if in USD
    if ($currency === 'usd') {
        $stmt = $pdo->query("SELECT price_usd FROM xmr_price ORDER BY updated_at DESC LIMIT 1");
        $xmr_price = $stmt->fetch(PDO::FETCH_ASSOC)['price_usd'];
        $amount = $amount / $xmr_price;
    }

    $stmt = $pdo->prepare("INSERT INTO ads (user_id, ad_type, amount, payment_method) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $ad_type, $amount, $payment_method])) {
        header("Location: profile.php");
        exit;
    } else {
        $error_message = "Failed to create the ad. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-bottom: none;
            background-color: #fff;
        }
        .tab.active {
            background-color: #007bff;
            color: #fff;
        }
        .tab-content {
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center">Create Ad</h1>
    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form method="POST" action="create_ad.php">
        <div class="tabs">
            <div class="tab active" onclick="switchTab('buy')">Buy</div>
            <div class="tab" onclick="switchTab('sell')">Sell</div>
        </div>
        <div class="tab-content">
            <input type="hidden" name="ad_type" id="ad_type" value="buy">
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <div class="input-group">
                    <input type="number" step="0.00000001" name="amount" id="amount" class="form-control" required>
                    <select name="currency" class="form-select">
                        <option value="xmr">Monero (XMR)</option>
                        <option value="usd">USD</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paypal">PayPal</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Ad</button>
        </div>
    </form>
</div>

<script>
    function switchTab(type) {
        document.getElementById('ad_type').value = type;
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        if (type === 'buy') {
            tabs[0].classList.add('active');
        } else {
            tabs[1].classList.add('active');
        }
    }
</script>
</body>
</html>
