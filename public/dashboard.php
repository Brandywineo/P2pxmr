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
        .toggle-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .toggle-buttons button {
            margin: 0 10px;
            width: 150px;
        }
        .filter-card {
            margin: 20px 0;
            padding: 20px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .listing-container {
            display: flex;
            justify-content: space-between;
        }
        .listing {
            width: 48%;
        }
        .card {
            margin-bottom: 15px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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

    <!-- Toggle Buttons -->
    <div class="toggle-buttons">
        <button id="buy-button" class="btn btn-primary" onclick="toggleView('buy')">Buy</button>
        <button id="sell-button" class="btn btn-outline-primary" onclick="toggleView('sell')">Sell</button>
    </div>

    <!-- Filter Card -->
    <div class="container">
        <div class="card filter-card">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="amount" class="form-label">Amount (USD)</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="payment" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment" name="payment">
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
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="filterAds()">Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listings -->
    <div class="container">
        <div class="listing-container">
            <!-- Buy Ads -->
            <div class="listing" id="buy-listing">
                <h4>Buy Ads</h4>
                <div id="buying-ads">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User1</h5>
                            <p class="card-text">Payment Method: PayPal</p>
                            <p class="card-text">Amount: $100</p>
                            <button class="btn btn-primary">Contact</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sell Ads -->
            <div class="listing" id="sell-listing" style="display: none;">
                <h4>Sell Ads</h4>
                <div id="selling-ads">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User2</h5>
                            <p class="card-text">Payment Method: Bank Transfer</p>
                            <p class="card-text">Amount: $200</p>
                            <button class="btn btn-primary">Contact</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function navigateTo(section) {
            if (section === 'wallet') {
                window.location.href = 'wallet.php';
            } else if (section === 'profile') {
                window.location.href = 'profile.php';
            }
        }

        function toggleView(view) {
            const buyButton = document.getElementById('buy-button');
            const sellButton = document.getElementById('sell-button');
            const buyListing = document.getElementById('buy-listing');
            const sellListing = document.getElementById('sell-listing');

            if (view === 'buy') {
                buyButton.classList.remove('btn-outline-primary');
                buyButton.classList.add('btn-primary');
                sellButton.classList.remove('btn-primary');
                sellButton.classList.add('btn-outline-primary');
                buyListing.style.display = 'block';
                sellListing.style.display = 'none';
            } else {
                sellButton.classList.remove('btn-outline-primary');
                sellButton.classList.add('btn-primary');
                buyButton.classList.remove('btn-primary');
                buyButton.classList.add('btn-outline-primary');
                sellListing.style.display = 'block';
                buyListing.style.display = 'none';
            }
        }

        function filterAds() {
            alert('Filtering ads based on criteria!');
        }
    </script>
</body>
</html>
