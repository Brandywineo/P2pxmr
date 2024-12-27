<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require '../src/config/db.php'; // Correct path to the database file

// Fetch the username
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? $user['username'] : 'User';
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
            transition: top 0.3s ease;
        }
        .header.hidden {
            top: -80px;
        }
        .filters, .action-buttons {
            margin: 20px 0;
            text-align: center;
        }
        .listing-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .listing-card:hover {
            transform: scale(1.02);
        }
        .infinite-scroll {
            overflow-y: auto;
            max-height: calc(100vh - 250px);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="icon">
            <i class="bi bi-person-circle"></i> Welcome, <?php echo htmlspecialchars($username); ?>
        </div>
        <div class="icon" onclick="location.href='wallet.php'">
            <i class="bi bi-wallet2"></i>
        </div>
    </div>

    <div class="container">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="location.href='create_ad.php'">
                <i class="bi bi-plus-circle"></i> Create Ad
            </button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <select class="form-select w-auto d-inline-block" id="sortFilter">
                <option value="cheapest">Cheapest to Most Expensive</option>
                <option value="expensive">Most Expensive to Cheapest</option>
            </select>
            <input type="text" class="form-control w-auto d-inline-block" id="searchFilter" placeholder="Search...">
        </div>

        <!-- Listings -->
        <div class="infinite-scroll" id="adListings">
            <!-- Ads will load here dynamically -->
        </div>
    </div>

    <script>
        let lastLoadedAdId = 0;
        const adListings = document.getElementById('adListings');
        const header = document.querySelector('.header');
        let lastScrollTop = 0;

        // Fetch Ads with Infinite Scrolling
        function fetchAds() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `../src/api/fetch_ads.php?last_id=${lastLoadedAdId}&sort=${getSortFilter()}`);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    if (data.length > 0) {
                        lastLoadedAdId = data[data.length - 1].id;
                        data.forEach(ad => appendAdCard(ad));
                    }
                }
            };
            xhr.send();
        }

        // Append Ad Card
        function appendAdCard(ad) {
            const card = document.createElement('div');
            card.className = 'listing-card';
            card.innerHTML = `
                <h5>${ad.ad_type.toUpperCase()} Ad</h5>
                <p><strong>Price:</strong> $${ad.price.toFixed(2)}</p>
                <p><strong>Percentage Over Market:</strong> ${ad.percentage}%</p>
                <button class="btn btn-${ad.ad_type === 'buy' ? 'primary' : 'success'}" onclick="goToOrder(${ad.id})">
                    ${ad.ad_type === 'buy' ? 'Buy' : 'Sell'}
                </button>
            `;
            adListings.appendChild(card);
        }

        // Redirect to Order Page
        function goToOrder(adId) {
            window.location.href = `../public/${adId.ad_type}_xmr.php?ad_id=${adId}`;
        }

        // Filter Logic
        function getSortFilter() {
            return document.getElementById('sortFilter').value;
        }

        document.getElementById('sortFilter').addEventListener('change', () => {
            adListings.innerHTML = '';
            lastLoadedAdId = 0;
            fetchAds();
        });

        // Infinite Scrolling
        adListings.addEventListener('scroll', () => {
            if (adListings.scrollTop + adListings.clientHeight >= adListings.scrollHeight) {
                fetchAds();
            }
        });

        // Header Show/Hide on Scroll
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop) {
                header.classList.add('hidden');
            } else {
                header.classList.remove('hidden');
            }
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });

        // Initial Load
        fetchAds();
    </script>
</body>
</html>
