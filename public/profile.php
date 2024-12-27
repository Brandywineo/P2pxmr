<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require '../src/config/db.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, trades_count, reviews_positive, reviews_negative, total_traded_amount FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user ads
$buy_ads_stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND ad_type = 'buy'");
$buy_ads_stmt->execute([$user_id]);
$buy_ads = $buy_ads_stmt->fetchAll(PDO::FETCH_ASSOC);

$sell_ads_stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND ad_type = 'sell'");
$sell_ads_stmt->execute([$user_id]);
$sell_ads = $sell_ads_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .balance {
            cursor: pointer;
            text-align: right;
            font-weight: bold;
            color: #343a40;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ads-container {
            margin-top: 20px;
        }

        .ad-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .btn-tab {
            margin: 0 5px;
        }

        .create-ad-btn {
            margin: 15px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- Account Balance -->
    <div class="balance" id="balance-display">
        <span id="balance-amount"><?php echo number_format($user['total_traded_amount'], 18); ?></span>
        <span id="balance-currency">XMR</span>
    </div>

    <!-- Username Card -->
    <div class="card mt-3">
        <div class="card-header">
            <h4><?php echo htmlspecialchars($user['username']); ?></h4>
        </div>
    </div>

    <!-- Stats -->
    <div class="card mt-3">
        <div class="card-body">
            <p>Number of Trades: <?php echo htmlspecialchars($user['trades_count']); ?></p>
            <p>Reviews: 
                <span class="text-success"><i class="bi bi-hand-thumbs-up-fill"></i> <?php echo htmlspecialchars($user['reviews_positive']); ?></span> 
                <span class="text-danger"><i class="bi bi-heartbreak-fill"></i> <?php echo htmlspecialchars($user['reviews_negative']); ?></span>
            </p>
        </div>
    </div>

    <!-- Ads Tabs -->
    <div class="ads-container">
        <div class="d-flex justify-content-center">
            <button class="btn btn-primary btn-tab" onclick="showAds('buy')">Buy Ads</button>
            <button class="btn btn-secondary btn-tab" onclick="showAds('sell')">Sell Ads</button>
        </div>

        <div id="buy-ads" class="ad-listings">
            <?php foreach ($buy_ads as $ad): ?>
                <div class="ad-card">
                    <p><strong>Price:</strong> $<?php echo htmlspecialchars($ad['price']); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($ad['payment_method']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="sell-ads" class="ad-listings" style="display: none;">
            <?php foreach ($sell_ads as $ad): ?>
                <div class="ad-card">
                    <p><strong>Price:</strong> $<?php echo htmlspecialchars($ad['price']); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($ad['payment_method']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Create Ad Button -->
    <div class="create-ad-btn">
        <button class="btn btn-success" onclick="location.href='public/create_ad.php'">
            <i class="bi bi-plus-circle"></i> Create Ad
        </button>
    </div>
</div>

<script>
    let currentCurrency = 'XMR';
    const balanceDisplay = document.getElementById('balance-display');
    const balanceAmount = document.getElementById('balance-amount');
    const balanceCurrency = document.getElementById('balance-currency');

    const xmrBalance = parseFloat(balanceAmount.innerHTML);
    const usdConversionRate = 150.00; // Example rate, replace with real-time value

    balanceDisplay.addEventListener('click', () => {
        if (currentCurrency === 'XMR') {
            balanceAmount.innerHTML = (xmrBalance * usdConversionRate).toFixed(2);
            balanceCurrency.innerHTML = 'USD';
            currentCurrency = 'USD';
        } else {
            balanceAmount.innerHTML = xmrBalance.toFixed(18);
            balanceCurrency.innerHTML = 'XMR';
            currentCurrency = 'XMR';
        }
    });

    function showAds(type) {
        document.getElementById('buy-ads').style.display = type === 'buy' ? 'block' : 'none';
        document.getElementById('sell-ads').style.display = type === 'sell' ? 'block' : 'none';
    }
</script>
</body>
</html>
