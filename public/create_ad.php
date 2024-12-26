<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require 'src/config/db.php'; // Corrected path

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_type = $_POST['ad_type']; // "buy" or "sell"
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    if (!empty($ad_type) && !empty($amount) && !empty($payment_method)) {
        try {
            // Insert the ad into the database
            $stmt = $pdo->prepare("INSERT INTO ads (user_id, ad_type, amount, payment_method) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $ad_type, $amount, $payment_method]);
            $message = "Ad created successfully!";
        } catch (Exception $e) {
            $message = "Error creating ad. Please try again.";
        }
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
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            border: none;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
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
                <div class="mb-3">
                    <label for="ad_type" class="form-label">Ad Type</label>
                    <select class="form-select" id="ad_type" name="ad_type" required>
                        <option value="">Select Ad Type</option>
                        <option value="buy">Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (USD)</label>
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">Select Payment Method</option>
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
</body>
</html>
