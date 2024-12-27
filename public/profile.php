<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require '../src/config/db.php'; // Adjusted path for public folder

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, trades_count, reviews_positive, reviews_negative, total_traded_amount FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch user ads
$buy_ads_stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND ad_type = 'buy' ORDER BY created_at DESC");
$buy_ads_stmt->execute([$user_id]);
$buy_ads = $buy_ads_stmt->fetchAll(PDO::FETCH_ASSOC);

$sell_ads_stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND ad_type = 'sell' ORDER BY created_at DESC");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .balance-toggle {
            cursor: pointer;
        }
        .profile-card {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .ads-section {
            margin-top: 20px;
        }
        .ad-card {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #ffffff;
        }
        .toggle-buttons {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Account Balance -->
        <div class="d-flex justify-content-end mt-4">
            <h3 id="balance" class="balance-toggle">
                <?php echo number_format($user['total_traded_amount'], 18); ?> XMR
            </h3>
        </div>

        <!-- Profile Card -->
        <div class="profile-card">
            <h4>Welcome, <?php echo htmlspecialchars($user['username']); ?></h4>
            <p>Trades: <?php echo $user['trades_count']; ?></p>
            <p>
                Reviews: 
                <span class="text-success"><i class="bi bi-hand-thumbs-up"></i> <?php echo $user['reviews_positive']; ?></span>
                <span class="text-danger"><i class="bi bi-heartbreak"></i> <?php echo $user['reviews_negative']; ?></span>
            </p>
        </div>

        <!-- Toggle Ads -->
        <div class="ads-section">
            <div class="toggle-buttons">
                <button class="btn btn-primary" onclick="showAds('buy')">Buy Ads</button>
                <button class="btn btn-secondary" onclick="showAds('sell')">Sell Ads</button>
                <button class="btn btn-success float-end" onclick="location.href='create_ad.php'">
                    <i class="bi bi-plus-circle"></i> Create Ad
                </button>
            </div>

            <!-- Buy Ads -->
            <div id="buy-ads">
                <h5>Buy Ads</h5>
                <?php foreach ($buy_ads as $ad) { ?>
                    <div class="ad-card">
                        <p><strong>Price:</strong> <?php echo $ad['price']; ?> USD/XMR</p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($ad['payment_method']); ?></p>
                        <button class="btn btn-primary" onclick="location.href='order.php?ad_id=<?php echo $ad['id']; ?>'">Buy</button>
                    </div>
                <?php } ?>
            </div>

            <!-- Sell Ads -->
            <div id="sell-ads" style="display: none;">
                <h5>Sell Ads</h5>
                <?php foreach ($sell_ads as $ad) { ?>
                    <div class="ad-card">
                        <p><strong>Price:</strong> <?php echo $ad['price']; ?> USD/XMR</p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($ad['payment_method']); ?></p>
                        <button class="btn btn-success" onclick="location.href='order.php?ad_id=<?php echo $ad['id']; ?>'">Sell</button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        const balance = document.getElementById('balance');
        let inXMR = true;

        balance.addEventListener('click', () => {
            const amount = <?php echo $user['total_traded_amount']; ?>;
            const usdRate = <?php echo $_SESSION['usd_rate'] ?? 1; ?>;
            
            if (inXMR) {
                balance.textContent = `${(amount * usdRate).toFixed(2)} USD`;
            } else {
                balance.textContent = `${amount.toFixed(18)} XMR`;
            }
            inXMR = !inXMR;
        });

        function showAds(type) {
            const buyAds = document.getElementById('buy-ads');
            const sellAds = document.getElementById('sell-ads');

            if (type === 'buy') {
                buyAds.style.display = 'block';
                sellAds.style.display = 'none';
            } else {
                buyAds.style.display = 'none';
                sellAds.style.display = 'block';
            }
        }
    </script>
</body>
</html>
