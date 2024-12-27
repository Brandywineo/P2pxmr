<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the correct database connection
require '../src/config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #343a40;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            transition: top 0.3s;
        }

        .header.hidden {
            top: -60px;
        }

        .filters {
            background-color: white;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .ad-listings {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .ad-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ad-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary, .btn-secondary, .btn-success {
            width: 100%;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header" id="header">
        <div onclick="location.href='profile.php'" style="cursor: pointer;">
            <i class="bi bi-person-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
        <div onclick="location.href='wallet.php'" style="cursor: pointer;">
            <i class="bi bi-wallet2"></i> Wallet
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="container">
        <div class="filters">
            <button class="btn btn-primary" onclick="filterAds('buy')">Buy</button>
            <button class="btn btn-secondary" onclick="filterAds('sell')">Sell</button>
            <button class="btn btn-success" onclick="location.href='create_ad.php'">
                <i class="bi bi-plus-circle"></i> Create Ad
            </button>
        </div>

        <!-- Ad Listings -->
        <div class="ad-listings" id="ad-listings">
            <!-- Ads will be loaded dynamically here -->
        </div>
    </div>

    <script>
        let lastId = 0;
        let currentFilter = 'buy';

        // Infinite Scroll
        window.addEventListener('scroll', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
                loadAds();
            }
        });

        // Header Show/Hide on Scroll
        let lastScroll = 0;
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            const currentScroll = window.scrollY;
            if (currentScroll > lastScroll && currentScroll > 60) {
                header.classList.add('hidden');
            } else {
                header.classList.remove('hidden');
            }
            lastScroll = currentScroll;
        });

        // Filter Ads
        function filterAds(type) {
            currentFilter = type;
            lastId = 0; // Reset ID for new filter
            document.getElementById('ad-listings').innerHTML = ''; // Clear previous ads
            loadAds();
        }

        // Load Ads Dynamically
        async function loadAds() {
            try {
                const response = await fetch(`../src/api/fetch_ads.php?last_id=${lastId}&sort=${currentFilter}`);
                const ads = await response.json();

                if (ads.length > 0) {
                    lastId = ads[ads.length - 1].id; // Update last ID
                    ads.forEach(ad => {
                        const adCard = document.createElement('div');
                        adCard.className = 'ad-card';
                        adCard.innerHTML = `
                            <div>
                                <p><strong>${ad.ad_type.toUpperCase()}</strong></p>
                                <p>Price: $${ad.price} (${ad.percentage}% over market)</p>
                            </div>
                            <button class="btn btn-${ad.ad_type === 'buy' ? 'primary' : 'secondary'}" onclick="location.href='${ad.ad_type}_xmr.php?ad_id=${ad.id}'">
                                ${ad.ad_type === 'buy' ? 'Buy' : 'Sell'}
                            </button>
                        `;
                        document.getElementById('ad-listings').appendChild(adCard);
                    });
                }
            } catch (error) {
                console.error('Error loading ads:', error);
            }
        }

        // Initial Load
        loadAds();
    </script>
</body>
</html>
