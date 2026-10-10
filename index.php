
<?php
require_once __DIR__ . "/admin/db.php";

$categories = $conn->query(
    "SELECT * FROM categories WHERE status = 1 ORDER BY id ASC"
);

if (!$categories) {
    die("Categories query failed: " . htmlspecialchars($conn->error));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brew Co. - Digital Menu</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>
<div class="app-container">

    <header class="header">
        <div class="icon-btn">☰</div>
        <div class="brand-title">
            <h1>BREW CO.</h1>
            <span>COFFEE &amp; MORE</span>
        </div>
        <div class="icon-btn">🛒</div>
    </header>

    <section class="hero-banner">
        <div class="hero-text">
            <h2>Good Coffee,<br>Good Day.</h2>
            <p>Made with love, just for you.</p>
            <span class="heart-icon">♡</span>
        </div>
        <img class="hero-img" src="images/coffee.jpg" alt="Good Coffee, Good Day">
    </section>

    <div class="menu-header">
        <h3>OUR MENU</h3>
    </div>

    <?php while ($category = $categories->fetch_assoc()): ?>
        <section class="category-section">
            <div class="category-header">
                <div class="category-title">
                    <?php echo htmlspecialchars($category['name']); ?>
                </div>
                <span class="chevron">›</span>
            </div>

            <?php
            $categoryId = (int) $category['id'];

            $items = $conn->query(
                "SELECT * FROM menu_items
                 WHERE category_id = $categoryId AND status = 1
                 ORDER BY id DESC"
            );
            ?>

            <?php if ($items && $items->num_rows > 0): ?>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <div class="item-card">
                        <?php if (!empty($item['image'])): ?>
                            <img
                                class="item-img"
                                src="images/<?php echo rawurlencode(basename($item['image'])); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>"
                            >
                        <?php else: ?>
                            <div class="item-img"></div>
                        <?php endif; ?>

                        <div class="item-info">
                            <div class="item-title">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </div>
                            <div class="item-desc">
                                <?php echo htmlspecialchars($item['description'] ?? ''); ?>
                            </div>
                        </div>

                        <div class="item-price">
                            $<?php echo number_format((float) $item['price'], 2); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No available items in this category.</p>
            <?php endif; ?>
        </section>
    <?php endwhile; ?>

    <div class="delivery-banner">
        <div class="delivery-left">
            <div class="delivery-icon">🛵</div>
            <div class="delivery-text">
                <h4>We deliver to you!</h4>
                <p>Fast &amp; fresh to your door.</p>
            </div>
        </div>
        <div class="chevron">›</div>
    </div>

</div>
</body>
</html>
