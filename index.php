
<?php
require_once "admin/db.php";

function getProductImage($item) {
    $name = strtolower(trim($item["name"] ?? ""));

    $images = [
        "latte" => "latte.jpg",
        "cappuccino" => "coffee.jpg",
        "iced coffee" => "iced-coffee.jpg",
        "americano" => "americano.jpg",
        "matcha latte" => "matcha.jpg",
        "chocolate cake" => "cake.jpg",
        "chocolate cookie" => "cake.jpg"
    ];

    return "images/" . ($images[$name] ?? "coffee.jpg");
}

function getCategoryName($category) {
    foreach (["name", "category_name", "title"] as $field) {
        if (!empty($category[$field])) {
            return $category[$field];
        }
    }

    return "MENU";
}

$categories = [];
$result = $conn->query("SELECT * FROM categories");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

function getItemsForCategory($conn, $categoryId) {
    $items = [];
    $stmt = $conn->prepare(
        "SELECT * FROM menu_items WHERE category_id = ?"
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

    <div class="header-icon cart-icon">🛒 <span>0</span></div>
</header>

<section class="hero">
    
    <img src="images/banner.png"
         alt="Good Coffee, Good Day."
         class="hero-img">

</section>


<main class="menu-container">
    <h2 class="menu-title">MENU</h2>

    <?php foreach ($categories as $category): ?>
        <?php
        $categoryId = (int)($category["id"] ?? 0);
        $categoryName = getCategoryName($category);
        $items = getItemsForCategory($conn, $categoryId);
        ?>

        <section class="menu-section" id="menu-<?php echo $categoryId; ?>">
            <div class="category-heading">
                <h3><?php echo htmlspecialchars(strtoupper($categoryName)); ?></h3>
                <a href="#menu-<?php echo $categoryId; ?>">View all ›</a>
            </div>

            <?php foreach ($items as $item): ?>
                <article class="menu-item">
                    <img
                        class="product-image"
                        src="<?php echo htmlspecialchars(getProductImage($item)); ?>"
                        alt="<?php echo htmlspecialchars($item["name"] ?? "Menu item"); ?>"
                        onerror="this.onerror=null;this.src='images/coffee.jpg';"
                    >

                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($item["name"] ?? ""); ?></h4>
                        <p>
                            <?php echo htmlspecialchars(
                                $item["description"] ?? "Freshly prepared with love."
                            ); ?>
                        </p>
                    </div>

                    <div class="item-price">
                        $<?php echo number_format((float)($item["price"] ?? 0), 2); ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
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
