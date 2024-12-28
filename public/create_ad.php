<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require '../src/config/db.php';

// Fetch the latest rate for dynamic conversion
$stmt = $pdo->query("SELECT usd_rate FROM rates ORDER BY updated_at DESC LIMIT 1");
$latest_rate = $stmt->fetch(PDO::FETCH_ASSOC)['usd_rate'] ?? 0;

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process ad creation
    $ad_type = $_POST['ad_type'];
    $xmr_amount = $_POST['xmr_amount'];
    $usd_amount = $_POST['usd_amount'];
    $rate_type = $_POST['rate_type'];
    $custom_rate = $_POST['custom_rate'];
    $time_limit = $_POST['time_limit'];
    $min_amount = $_POST['min_amount'];
    $max_amount = $_POST['max_amount'];
    $payment_methods = implode(',', $_POST['payment_methods']);
    $user_id = $_SESSION['user_id'];

    if (!empty($ad_type) && !empty($xmr_amount) && !empty($rate_type)) {
        $stmt = $pdo->prepare(
            "INSERT INTO ads (user_id, ad_type, xmr_amount, usd_amount, rate_type, custom_rate, time_limit, min_amount, max_amount, payment_methods) 
            VALUES (:user_id, :ad_type, :xmr_amount, :usd_amount, :rate_type, :custom_rate, :time_limit, :min_amount, :max_amount, :payment_methods)"
        );
        $stmt->execute([
            'user_id' => $user_id,
            'ad_type' => $ad_type,
            'xmr_amount' => $xmr_amount,
            'usd_amount' => $usd_amount,
            'rate_type' => $rate_type,
            'custom_rate' => $custom_rate,
            'time_limit' => $time_limit,
            'min_amount' => $min_amount,
            'max_amount' => $max_amount,
            'payment_methods' => $payment_methods,
        ]);
        $message = "Ad created successfully!";
        header("Location: profile.php");
        exit;
    } else {
        $message = "Please fill in all required fields.";
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
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .small-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center my-4">Create New Ad</h2>
    <?php if ($message) : ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- First Card -->
    <div class="card p-4">
        <div class="d-flex justify-content-between">
            <button class="btn btn-outline-primary" id="adTypeToggle">Buy</button>
            <p class="small-text">1 XMR = $<?php echo number_format($latest_rate, 2); ?></p>
        </div>
        <div class="mt-3">
            <p class="fw-bold" id="adAction">I want to buy</p>
            <div class="d-flex align-items-center">
                <input type="number" id="xmrAmount" class="form-control me-2" placeholder="Enter XMR amount" required>
                <span class="fw-bold mx-2">⇄</span>
                <input type="number" id="usdAmount" class="form-control" placeholder="Equivalent in USD" required>
            </div>
        </div>
    </div>

    <!-- Second Card -->
    <div class="card p-4 mt-3">
        <h5>Pricing Options</h5>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="rate_type" id="fixedRate" value="fixed" checked>
            <label class="form-check-label" for="fixedRate">Fixed Rate</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="rate_type" id="floatRate" value="float">
            <label class="form-check-label" for="floatRate">Float Rate (Market Dependent)</label>
        </div>
        <div class="mt-3">
            <label for="customRate" class="form-label">Custom Rate (Optional)</label>
            <input type="number" id="customRate" class="form-control" placeholder="Set your own rate in USD">
        </div>
    </div>

    <!-- Third Card -->
    <div class="card p-4 mt-3">
        <h5>Transaction Settings</h5>
        <div class="mb-3">
            <label for="timeLimit" class="form-label">Time Limit (15-30 mins)</label>
            <input type="number" id="timeLimit" class="form-control" min="15" max="30" placeholder="Enter time limit in minutes">
        </div>
        <div class="mb-3">
            <label for="minAmount" class="form-label">Minimum Trade Amount (USD)</label>
            <input type="number" id="minAmount" class="form-control" placeholder="Enter minimum trade amount">
        </div>
        <div class="mb-3">
            <label for="maxAmount" class="form-label">Maximum Trade Amount (USD)</label>
            <input type="number" id="maxAmount" class="form-control" placeholder="Enter maximum trade amount">
        </div>
    </div>

    <!-- Fourth Card -->
    <div class="card p-4 mt-3">
        <h5>Payment Methods</h5>
        <div class="mb-3">
            <label for="paymentMethods" class="form-label">Select Payment Methods</label>
            <select id="paymentMethods" class="form-select" multiple>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="paypal">PayPal</option>
                <option value="crypto_wallet">Crypto Wallet</option>
                <option value="venmo">Venmo</option>
                <option value="cash_app">Cash App</option>
            </select>
        </div>
        <button class="btn btn-success w-100" id="addPaymentMethod">Add Payment Method</button>
    </div>

    <button class="btn btn-primary w-100 mt-4">Submit Ad</button>
</div>

<script>
    const xmrAmount = document.getElementById('xmrAmount');
    const usdAmount = document.getElementById('usdAmount');
    const rate = <?php echo $latest_rate; ?>;

    xmrAmount.addEventListener('input', () => {
        usdAmount.value = (xmrAmount.value * rate).toFixed(2);
    });

    usdAmount.addEventListener('input', () => {
        xmrAmount.value = (usdAmount.value / rate).toFixed(12);
    });

    const adTypeToggle = document.getElementById('adTypeToggle');
    const adAction = document.getElementById('adAction');

    adTypeToggle.addEventListener('click', () => {
        if (adTypeToggle.textContent === 'Buy') {
            adTypeToggle.textContent = 'Sell';
            adAction.textContent = 'I want to sell';
        } else {
            adTypeToggle.textContent = 'Buy';
            adAction.textContent = 'I want to buy';
        }
    });
</script>
</body>
</html>
