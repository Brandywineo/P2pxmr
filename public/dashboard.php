<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
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
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .icon {
            cursor: pointer;
            font-size: 24px;
        }
        .content {
            margin: 20px;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 18px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <span class="icon" onclick="navigateTo('profile')">👤</span>
        <h5>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h5>
        <span class="icon" onclick="navigateTo('wallet')">💼</span>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Buy/Sell Monero Card -->
        <div class="card">
            <div class="card-header">Buy/Sell Monero</div>
            <div class="card-body">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="buy">Buy</option>
                                <option value="sell">Sell</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payment" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment" name="payment">
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                                <option value="crypto">Crypto</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" onclick="filterAds()">Filter Ads</button>
                </form>
            </div>
        </div>

        <!-- Listings -->
        <div class="card">
            <div class="card-header">Buying Ads</div>
            <div class="card-body" id="buying-ads">
                <!-- Example buying ads -->
                <p>No buying ads available.</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Selling Ads</div>
            <div class="card-body" id="selling-ads">
                <!-- Example selling ads -->
                <p>No selling ads available.</p>
            </div>
        </div>
    </div>

    <script>
        // Navigation to profile and wallet
        function navigateTo(section) {
            if (section === 'wallet') {
                window.location.href = 'wallet.php';
            } else if (section === 'profile') {
                window.location.href = 'profile.php';
            }
        }

        // Simulate filtering ads (placeholder functionality)
        function filterAds() {
            const type = document.getElementById('type').value;
            const payment = document.getElementById('payment').value;

            let buyingAds = `
                <p>Buying ${payment} ads filtered for type: ${type}</p>
            `;
            let sellingAds = `
                <p>Selling ${payment} ads filtered for type: ${type}</p>
            `;

            if (type === 'buy') {
                document.getElementById('buying-ads').innerHTML = buyingAds;
                document.getElementById('selling-ads').innerHTML = `<p>No selling ads available.</p>`;
            } else {
                document.getElementById('selling-ads').innerHTML = sellingAds;
                document.getElementById('buying-ads').innerHTML = `<p>No buying ads available.</p>`;
            }
        }
    </script>
</body>
</html>
