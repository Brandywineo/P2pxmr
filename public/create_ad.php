<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require '../src/config/db.php';

// Initialize variables
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_type = $_POST['ad_type']; // "buy" or "sell"
    $amount_usd = $_POST['amount_usd'];
    $amount_xmr = $_POST['amount_xmr'];
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (!empty($ad_type) && (!empty($amount_usd) || !empty($amount_xmr)) && !empty($payment_method)) {
        // Store the amount in USD or XMR
        $amount = $amount_usd ? $amount_usd : $amount_xmr;

        // Insert the ad into the database
        $stmt = $pdo->prepare("INSERT INTO ads (user_id, ad_type, amount, payment_method) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $ad_type, $amount, $payment_method])) {
            $message = "Ad created successfully!";
            header("Location: profile.php");
            exit;
        } else {
            $message = "Error creating ad. Please try again.";
        }
    } else {
        $message = "All fields are required.";
    }
}

// Fetch the latest Monero price
$price_stmt = $pdo->query("SELECT price_usd FROM monero_price ORDER BY updated_at DESC LIMIT 1");
$price_data = $price_stmt->fetch(PDO::FETCH_ASSOC);
$monero_price = $price_data ? $price_data['price_usd'] : 'N/A';
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
        .card {
            margin: 50px auto;
            max-width: 800px;
            padding: 20px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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
            background-color: #f8f9fa;
        }
        .tab.active {
            background-color: #007bff;
            color: white;
        }
        .price-info {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header bg-dark text-white p-3 d-flex justify-content-between">
        <div onclick="location.href='profile.php'" style="cursor: pointer;">Profile</div>
        <div>Monero Price: $<?php echo htmlspecialchars($monero_price); ?></div>
        <div onclick="location.href='wallet.php'" style="cursor: pointer;">Wallet</div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <h4 class="text-center mb-4">Create New Ad</h4>
            <?php if (!empty($message)) { ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <!-- Ad Type Tabs -->
            <div class="tabs">
                <div id="buy-tab" class="tab active" onclick="selectTab('buy')">Buy</div>
                <div id="sell-tab" class="tab" onclick="selectTab('sell')">Sell</div>
            </div>

            <!-- Form -->
            <form method="POST">
                <input type="hidden" id="ad_type" name="ad_type" value="buy">
                <div class="price-info">Current Monero Price: $<?php echo htmlspecialchars($monero_price); ?></div>

                <!-- Amount -->
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <div class="d-flex gap-2">
                        <input type="number" class="form-control" id="amount_usd" name="amount_usd" placeholder="Enter amount in USD">
                        <input type="number" class="form-control" id="amount_xmr" name="amount_xmr" placeholder="Enter amount in XMR">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="paypal">PayPal</option>
                        <option value="crypto">Crypto Wallet</option>
                        <option value="venmo">Venmo</option>
                        <option value="cashapp">Cash App</option>
                        <option value="skrill">Skrill</option>
                        <option value="western_union">Western Union</option>
                        <option value="zelle">Zelle</option>
                        <option value="alipay">Alipay</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Create Ad</button>
            </form>
        </div>
    </div>

    <script>
        function selectTab(type) {
            const buyTab = document.getElementById('buy-tab');
            const sellTab = document.getElementById('sell-tab');
            const adTypeInput = document.getElementById('ad_type');

            if (type === 'buy') {
                buyTab.classList.add('active');
                sellTab.classList.remove('active');
                adTypeInput.value = 'buy';
            } else {
                sellTab.classList.add('active');
                buyTab.classList.remove('active');
                adTypeInput.value = 'sell';
            }
        }
    </script>
</body>
</html>
