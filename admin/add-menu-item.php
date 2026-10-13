
<?php
include "db.php";

$message = "";

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $category_id = (int) ($_POST["category_id"] ?? 0);
    $image = trim($_POST["image"] ?? "");

    if (
        $name === "" ||
        $price === "" ||
        !is_numeric($price) ||
        (float)$price < 0 ||
        $category_id <= 0
    ) {
        $message = "Please enter a valid name, price, and category.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO menu_items
            (name, description, price, image, category_id)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssdsi",
            $name,
            $description,
            $price,
            $image,
            $category_id
        );

        if ($stmt->execute()) {
            header("Location: menu-items.php");
            exit;
        } else {
            $message = "Error adding item: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f1eb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 550px;
            margin: 20px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #00000015;
        }

        h1 {
            color: #5c3825;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        button {
            margin-top: 20px;
            padding: 12px 18px;
            background: #6f4e37;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #4b3223;
        }

        .message {
            color: #b00020;
        }

        a {
            color: #6f4e37;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Add Menu Item</h1>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Item Name</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3"></textarea>

        <label for="price">Price</label>
        <input type="number" id="price" name="price"
               min="0" step="0.01" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <option value="<?= (int)$category["id"] ?>">
                    <?= htmlspecialchars($category["name"]) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="image">Image Path</label>
        <input type="text" id="image" name="image"
               placeholder="images/coffee.jpg">

        <button type="submit">Add Menu Item</button>
    </form>

    <p><a href="menu-items.php">Back to Menu Items</a></p>
</div>

</body>
</html>
