<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require './src/config/db.php';

// Fetch user information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, balance_xmr, trades_count, reviews_positive, reviews_negative FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user ads
$buy_ads = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND ad_type = 'buy' ORDER BY created_at DESC");
$buy_ads->execute([$user_id]);
$sell_ads = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND ad_type = 'sell' ORDER BY created_at DESC");
$sell_ads->execute([$user_id]);

// Fetch the latest Monero price
$price_stmt = $pdo->query("SELECT price_usd FROM monero_price ORDER BY updated_at DESC LIMIT 1");
$price_data = $price_stmt->fetch(PDO::FETCH_ASSOC);
$monero_price = $price_data ? $price_data['price_usd'] : 0.00;

// Helper functions
function format_balance($balance, $is_xmr = true, $price_usd = 0) {
    return $is_xmr
        ? number_format($balance, 18) . ' XMR'
        : '$' . number_format($balance * $price_usd, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .balance-card, .username-card, .trades-card, .ads-card {
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .tab {
            cursor: pointer;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .tab.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Account Balance -->
    <div class="balance-card text-center">
        <h5>Account Balance</h5>
        <h4 id="balance" style="cursor: pointer;" onclick="toggleBalance()">
            <?php echo format_balance($user['balance_xmr'], true, $monero_price); ?>
        </h4>
    </div>

    <!-- Username Card -->
    <div class="username-card text-center">
        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
    </div>

    <!-- Trades & Reviews -->
    <div class="trades-card text-center">
        <p>
            Number of Trades: <strong><?php echo htmlspecialchars($user['trades_count']); ?></strong>
        </p>
        <p>
            Trades Volume: 
            <strong id="trade-volume" style="cursor: pointer;" onclick="toggleTradeVolume()">
                <?php echo format_balance($user['balance_xmr'], true, $monero_price); ?>
            </strong>
        </p>
        <p>
            Reviews: 
            <span class="text-success">👍 <?php echo htmlspecialchars($user['reviews_positive']); ?></span>
            <span class="text-danger">💔 <?php echo htmlspecialchars($user['reviews_negative']); ?></span>
        </p>
    </div>

    <!-- Ads Section -->
    <div class="ads-card">
        <h4 class="text-center">My Ads</h4>
        <!-- Tabs -->
        <div class="tabs d-flex justify-content-center mb-4">
            <div id="buy-tab" class="tab active" onclick="showAds('buy')">Buy Ads</div>
            <div id="sell-tab" class="tab" onclick="showAds('sell')">Sell Ads</div>
        </div>

        <!-- Ads Listings -->
        <div id="buy-ads">
            <?php while ($ad = $buy_ads->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><strong>Amount:</strong> $<?php echo htmlspecialchars($ad['amount']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($ad['payment_method']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($ad['created_at']); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="sell-ads" style="display: none;">
            <?php while ($ad = $sell_ads->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><strong>Amount:</strong> $<?php echo htmlspecialchars($ad['amount']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($ad['payment_method']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($ad['created_at']); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Create Ad Button -->
    <div class="text-center mt-4">
        <button class="btn btn-primary" onclick="location.href='create_ad.php'">+ Create Ad</button>
    </div>

    <script>
        let isXmr = true;

        function toggleBalance() {
            const balanceElem = document.getElementById('balance');
            isXmr = !isXmr;
            const balance = <?php echo $user['balance_xmr']; ?>;
            const priceUsd = <?php echo $monero_price; ?>;
            balanceElem.textContent = isXmr
                ? `${balance.toFixed(18)} XMR`
                : `$${(balance * priceUsd).toFixed(2)}`;
        }

        function toggleTradeVolume() {
            const volumeElem = document.getElementById('trade-volume');
            isXmr = !isXmr;
            const balance = <?php echo $user['balance_xmr']; ?>;
            const priceUsd = <?php echo $monero_price; ?>;
            volumeElem.textContent = isXmr
                ? `${balance.toFixed(18)} XMR`
                : `$${(balance * priceUsd).toFixed(2)}`;
        }

        function showAds(type) {
            const buyAds = document.getElementById('buy-ads');
            const sellAds = document.getElementById('sell-ads');
            const buyTab = document.getElementById('buy-tab');
            const sellTab = document.getElementById('sell-tab');

            if (type === 'buy') {
                buyAds.style.display = 'block';
                sellAds.style.display = 'none';
                buyTab.classList.add('active');
                sellTab.classList.remove('active');
            } else {
                sellAds.style.display = 'block';
                buyAds.style.display = 'none';
                sellTab.classList.add('active');
                buyTab.classList.remove('active');
            }
        }
    </script>
</body>
</html>
