<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require '../src/config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_type = $_POST['ad_type']; // "buy" or "sell"
    $amount = $_POST['amount'];
    $amount_type = $_POST['amount_type']; // "monero" or "usd"
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    if (!empty($ad_type) && !empty($amount) && !empty($payment_method)) {
        // Insert the ad into the database
        $stmt = $pdo->prepare("INSERT INTO ads (user_id, ad_type, amount, amount_type, payment_method) VALUES (:user_id, :ad_type, :amount, :amount_type, :payment_method)");
        $stmt->execute([
            'user_id' => $user_id,
            'ad_type' => $ad_type,
            'amount' => $amount,
            'amount_type' => $amount_type,
            'payment_method' => $payment_method,
        ]);
        $message = "Ad created successfully!";
        header("Location: profile.php");
        exit;
    } else {
        $message = "All fields are required.";
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
        .card {
            margin: 30px auto;
            padding: 20px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
        }
        .tab.active {
            background-color: #007bff;
            color: white;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h4 class="text-center mb-4">Create New Ad</h4>
            <?php if (!empty($message)) { ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>
            <form method="POST">
                <!-- Tabs for Ad Type -->
                <div class="tabs">
                    <div class="tab active" id="buy-tab" onclick="selectAdType('buy')">Buy</div>
                    <div class="tab" id="sell-tab" onclick="selectAdType('sell')">Sell</div>
                </div>
                <input type="hidden" name="ad_type" id="ad_type" value="buy">

                <!-- Amount Input -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                        <select class="form-select" id="amount_type" name="amount_type">
                            <option value="monero">XMR</option>
                            <option value="usd">USD</option>
                        </select>
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
        function selectAdType(type) {
            document.getElementById('ad_type').value = type;
            document.getElementById('buy-tab').classList.remove('active');
            document.getElementById('sell-tab').classList.remove('active');
            document.getElementById(type + '-tab').classList.add('active');
        }
    </script>
</body>
</html>
