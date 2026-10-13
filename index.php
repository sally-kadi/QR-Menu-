```php
<?php
require_once __DIR__ . "/admin/db.php";

/*
|--------------------------------------------------------------------------
| Product image
|--------------------------------------------------------------------------
*/
function getProductImage($item) {
    $image = trim($item["image"] ?? "");

    if ($image !== "") {
        // Accept a path stored in the database, e.g. images/coffee.jpg
        return $image;
    }

    $name = strtolower(trim($item["name"] ?? ""));

    $images = [
        "latte" => "images/latte.jpg",
        "cappuccino" => "images/coffee.jpg",
        "iced coffee" => "images/iced-coffee.jpg",
        "americano" => "images/americano.jpg",
        "matcha latte" => "images/matcha.jpg",
        "chocolate cake" => "images/cake.jpg",
        "chocolate cookie" => "images/cake.jpg"
    ];

    return $images[$name] ?? "images/coffee.jpg";
}

/*
|--------------------------------------------------------------------------
| Load categories
|--------------------------------------------------------------------------
*/
$categories = [];

$categoryResult = $conn->query(
    "SELECT id, name FROM categories ORDER BY id ASC"
);

if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| Load active products for a category
|--------------------------------------------------------------------------
*/
function getItemsForCategory($conn, $categoryId) {
    $items = [];

    $stmt = $conn->prepare(
        "SELECT id, name, description, price, image, status
         FROM menu_items
         WHERE category_id = ? AND (status = 1 OR status IS NULL)
         ORDER BY id DESC"
    );

    if ($stmt) {
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        $stmt->close();
    }

    return $items;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Brew Co. - Digital Menu</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="site-header">
    <div class="header-icon">☰</div>

    <div class="logo">
        <div class="logo-cup">☕</div>
        <h1>BREW CO.</h1>
        <p>COFFEE &amp; MORE</p>
    </div>

    <div class="header-icon cart-icon">
        🛒 <span>0</span>
    </div>
</header>

<section class="hero">
    <img
        src="images/banner.png"
        alt="Good Coffee, Good Day."
        class="hero-img"
    >
</section>

<main class="menu-container">
    <h2 class="menu-title">MENU</h2>

    <?php if (empty($categories)): ?>

        <p>No categories available yet.</p>

    <?php else: ?>

        <?php foreach ($categories as $category): ?>

            <?php
            $categoryId = (int)$category["id"];
            $categoryName = $category["name"];
            $items = getItemsForCategory($conn, $categoryId);

            // Do not display empty categories on the public menu.
            if (empty($items)) {
                continue;
            }
            ?>

            <section
                class="menu-section"
                id="menu-<?= $categoryId ?>"
            >
                <div class="category-heading">
                    <h3>
                        <?= htmlspecialchars(strtoupper($categoryName)) ?>
                    </h3>

                    <a href="#menu-<?= $categoryId ?>">
                        View all ›
                    </a>
                </div>

                <?php foreach ($items as $item): ?>

                    <?php $imagePath = getProductImage($item); ?>

                    <article class="menu-item">

                        <img
                            class="product-image"
                            src="<?= htmlspecialchars($imagePath) ?>"
                            alt="<?= htmlspecialchars($item["name"]) ?>"
                            onerror="this.onerror=null;this.src='images/coffee.jpg';"
                        >

                        <div class="item-info">
                            <h4>
                                <?= htmlspecialchars($item["name"]) ?>
                            </h4>

                            <p>
                                <?php
                                $description = trim(
                                    $item["description"] ?? ""
                                );

                                echo htmlspecialchars(
                                    $description !== ""
                                        ? $description
                                        : "Freshly prepared with love."
                                );
                                ?>
                            </p>
                        </div>

                        <div class="item-price">
                            $<?= number_format((float)$item["price"], 2) ?>
                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endforeach; ?>

    <?php endif; ?>
</main>

<footer class="site-footer">

    <div class="delivery">
        <span class="delivery-icon">🛵</span>

        <div>
            <h3>We deliver to you!</h3>
            <p>Fast &amp; fresh to your door.</p>
        </div>

        <span class="delivery-arrow">›</span>
    </div>

    <div class="footer-brand">
        <h2>BREW CO.</h2>
        <p>Good Coffee, Good Day.</p>
        <p>Thank you for visiting us!</p>
    </div>

</footer>

</body>
</html>
```