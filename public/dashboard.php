<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the correct database connection
require '../src/config/db.php';

// Fetch Buy Ads
$buy_ads = $pdo->query("SELECT * FROM ads WHERE ad_type = 'buy' ORDER BY created_at DESC");

// Fetch Sell Ads
$sell_ads = $pdo->query("SELECT * FROM ads WHERE ad_type = 'sell' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
        }
        .header .icon {
            cursor: pointer;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        .listings {
            display: flex;
            gap: 20px;
        }
        .listings > div {
            flex: 1;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .listing-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding: 10px 0;
        }
        .listing-item:last-child {
            border-bottom: none;
        }
        .btn-action {
            padding: 5px 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="icon" onclick="location.href='profile.php'">
            <i class="bi bi-person-circle"></i> Profile
        </div>
        <div class="icon" onclick="location.href='create_ad.php'">
            <i class="bi bi-plus-circle"></i> Create Ad
        </div>
        <div class="icon" onclick="location.href='wallet.php'">
            <i class="bi bi-wallet2"></i> Wallet
        </div>
    </div>

    <div class="container">
        <!-- Filters -->
        <div class="filters">
            <button class="btn btn-primary" onclick="showListings('buy')">Buy</button>
            <button class="btn btn-secondary" onclick="showListings('sell')">Sell</button>
        </div>

        <!-- Listings -->
        <div class="listings">
            <!-- Buy Listings -->
            <div id="buy-listing">
                <h4>Buy Ads</h4>
                <?php while ($buy_ad = $buy_ads->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="listing-item">
                        <div>
                            <p><strong>User ID: <?php echo htmlspecialchars($buy_ad['user_id']); ?></strong></p>
                            <p>Payment Method: <?php echo htmlspecialchars($buy_ad['payment_method']); ?></p>
                            <p>Amount: $<?php echo htmlspecialchars($buy_ad['amount']); ?></p>
                        </div>
                        <button class="btn btn-primary btn-action" onclick="goToTransaction('buy', <?php echo $buy_ad['amount']; ?>)">Buy</button>
                    </div>
                <?php } ?>
            </div>

            <!-- Sell Listings -->
            <div id="sell-listing" style="display: none;">
                <h4>Sell Ads</h4>
                <?php while ($sell_ad = $sell_ads->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="listing-item">
                        <div>
                            <p><strong>User ID: <?php echo htmlspecialchars($sell_ad['user_id']); ?></strong></p>
                            <p>Payment Method: <?php echo htmlspecialchars($sell_ad['payment_method']); ?></p>
                            <p>Amount: $<?php echo htmlspecialchars($sell_ad['amount']); ?></p>
                        </div>
                        <button class="btn btn-success btn-action" onclick="goToTransaction('sell', <?php echo $sell_ad['amount']; ?>)">Sell</button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        function showListings(type) {
            const buyList = document.getElementById('buy-listing');
            const sellList = document.getElementById('sell-listing');

            if (type === 'buy') {
                buyList.style.display = 'block';
                sellList.style.display = 'none';
            } else {
                buyList.style.display = 'none';
                sellList.style.display = 'block';
            }
        }

        function goToTransaction(type, amount) {
            const url = type === 'buy' ? 'buy_xmr.php' : 'sell_xmr.php';
            window.location.href = `${url}?amount=${amount}`;
        }
    </script>
</body>
</html>
