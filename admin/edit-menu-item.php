
<?php
include "db.php";

$message = "";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("Invalid menu item ID.");
}

$stmt = $conn->prepare(
    "SELECT id, name, description, price, image, category_id
     FROM menu_items WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    die("Menu item not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $image = trim($_POST["image"] ?? "");
    $category_id = (int)($_POST["category_id"] ?? 0);

    if (
        $name === "" ||
        $price === "" ||
        !is_numeric($price) ||
        (float)$price < 0 ||
        $category_id <= 0
    ) {
        $message = "Please enter a valid name, price, and category.";
    } else {
        $check = $conn->prepare("SELECT id FROM categories WHERE id = ?");
        $check->bind_param("i", $category_id);
        $check->execute();
        $validCategory = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$validCategory) {
            $message = "Please select a valid category.";
        } else {
            $price = (float)$price;

            $update = $conn->prepare(
                "UPDATE menu_items
                 SET name = ?, description = ?, price = ?,
                     image = ?, category_id = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "ssdsii",
                $name,
                $description,
                $price,
                $image,
                $category_id,
                $id
            );

            if ($update->execute()) {
                $update->close();
                header("Location: menu-items.php");
                exit;
            } else {
                $message = "Update failed: " . $update->error;
                $update->close();
            }
        }
    }
}

$categories = $conn->query(
    "SELECT id, name FROM categories ORDER BY name"
);

if (!$categories) {
    die("Could not load categories: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item - Brew Co.</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f2ec;
            color: #38251c;
            padding: 30px;
            margin: 0;
        }

        .form-container {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #00000015;
        }

        h1 {
            color: #5b3827;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        button {
            background: #5b3827;
            color: white;
            padding: 12px 20px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #3e281d;
        }

        .message {
            color: #b00020;
        }

        a {
            color: #805334;
        }

        .current-image {
            display: block;
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin: 10px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Menu Item</h1>

        <?php if ($message !== ""): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Menu Item Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($item["name"]) ?>"
                required
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="3"
            ><?= htmlspecialchars($item["description"] ?? "") ?></textarea>

            <label for="price">Price</label>
            <input
                type="number"
                id="price"
                name="price"
                min="0"
                step="0.01"
                value="<?= htmlspecialchars((string)$item["price"]) ?>"
                required
            >

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>

                <?php while ($category = $categories->fetch_assoc()): ?>
                    <option
                        value="<?= (int)$category["id"] ?>"
                        <?= (int)$category["id"] === (int)$item["category_id"] ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($category["name"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="image">Image Path</label>
            <input
                type="text"
                id="image"
                name="image"
                value="<?= htmlspecialchars($item["image"] ?? "") ?>"
                placeholder="images/coffee.jpg"
            >

            <?php
            $imagePath = trim($item["image"] ?? "");
            $imageFile = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . str_replace("/", DIRECTORY_SEPARATOR, $imagePath);
            ?>

            <?php if ($imagePath !== "" && is_file($imageFile)): ?>
                <p>Current Image:</p>
                <img
                    class="current-image"
                    src="../<?= htmlspecialchars($imagePath) ?>"
                    alt="Current menu item image"
                >
            <?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>

        <p><a href="menu-items.php">Back to Menu Items</a></p>
    </div>
</body>
</html>
