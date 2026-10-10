
<?php
include "db.php";

$categoriesResult = $conn->query(
    "SELECT COUNT(*) AS total FROM categories"
);
$totalCategories = $categoriesResult->fetch_assoc()["total"];

$itemsResult = $conn->query(
    "SELECT COUNT(*) AS total FROM menu_items"
);
$totalItems = $itemsResult->fetch_assoc()["total"];

$activeResult = $conn->query(
    "SELECT COUNT(*) AS total FROM menu_items WHERE status = 1"
);
$activeItems = $activeResult->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Brew Co.</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f7f4ef;
            color: #2e241f;
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #65452f;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .card {
            padding: 25px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 3px 12px #39271915;
        }

        .card h2 {
            margin: 0 0 12px;
            font-size: 17px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #8b5e3c;
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .links a {
            padding: 13px 18px;
            border-radius: 8px;
            background: #65452f;
            color: white;
            text-decoration: none;
        }

        .links a:hover {
            background: #8b5e3c;
        }
    </style>
</head>
<body>

    <h1>Brew Co. Admin Dashboard</h1>
    <p>Welcome to your menu management system.</p>

    <div class="cards">
        <div class="card">
            <h2>Total Categories</h2>
            <div class="number">
                <?= (int) $totalCategories ?>
            </div>
        </div>

        <div class="card">
            <h2>Total Menu Items</h2>
            <div class="number">
                <?= (int) $totalItems ?>
            </div>
        </div>

        <div class="card">
            <h2>Active Menu Items</h2>
            <div class="number">
                <?= (int) $activeItems ?>
            </div>
        </div>
    </div>

    <h2>Quick Links</h2>

    <div class="links">
        <a href="categories.php">Manage Categories</a>
        <a href="items.php">Manage Menu Items</a>
        <a href="../index.php">View Customer Menu</a>
    </div>

</body>
</html>
