```php
<?php
include "db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid item ID.");
}

$id = (int) $_GET['id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);

    if ($category_id <= 0 || $name === '' || $price < 0) {
        $message = "Please enter valid item details.";
    } else {
        $check = $conn->prepare("SELECT id FROM categories WHERE id = ?");
        $check->bind_param("i", $category_id);
        $check->execute();
        $category_exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$category_exists) {
            $message = "Please select a valid category.";
        } else {
            $image_name = "";

            if (isset($_FILES['image']) &&
                $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

                if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $original_name = $_FILES['image']['name'];
                    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

                    if (in_array($extension, $allowed, true)) {
                        $image_name = uniqid('item_', true) . '.' . $extension;
                        $upload_path = __DIR__ . '/../images/' . $image_name;

                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $message = "Image upload failed.";
                        }
                    } else {
                        $message = "Please choose a JPG, PNG, or WEBP image.";
                    }
                } else {
                    $message = "There was a problem uploading the image.";
                }
            }

            if ($message === "") {
                if ($image_name !== "") {
                    $stmt = $conn->prepare(
                        "UPDATE menu_items
                         SET category_id = ?, name = ?, description = ?, price = ?, image = ?
                         WHERE id = ?"
                    );
                    $stmt->bind_param(
                        "issdsi",
                        $category_id,
                        $name,
                        $description,
                        $price,
                        $image_name,
                        $id
                    );
                } else {
                    $stmt = $conn->prepare(
                        "UPDATE menu_items
                         SET category_id = ?, name = ?, description = ?, price = ?
                         WHERE id = ?"
                    );
                    $stmt->bind_param(
                        "issdi",
                        $category_id,
                        $name,
                        $description,
                        $price,
                        $id
                    );
                }

                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: items.php");
                    exit;
                } else {
                    $message = "Could not update item: " . $stmt->error;
                    $stmt->close();
                }
            }
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    die("Menu item not found.");
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f1eb;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,.1);
        }

        h1 {
            color: #6f4e37;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            min-height: 90px;
        }

        button, .cancel {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background: #8b6b61;
            color: white;
        }

        .cancel {
            background: #ddd;
            color: #333;
        }

        .message {
            color: #b42318;
        }

        .current-image {
            display: block;
            max-width: 120px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Edit Menu Item</h1>

    <?php if ($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" required>
            <option value="">Select Category</option>

            <?php while ($category = $categories->fetch_assoc()): ?>
                <option
                    value="<?= (int)$category['id'] ?>"
                    <?= (int)$category['id'] === (int)$item['category_id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="name">Item Name</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($item['name'] ?? '') ?>"
            required
        >

        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>

        <label for="price">Price ($)</label>
        <input
            type="number"
            id="price"
            name="price"
            min="0"
            step="0.01"
            value="<?= htmlspecialchars($item['price'] ?? '0') ?>"
            required
        >

        <label for="image">Change Image (optional)</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">

        <?php if (!empty($item['image'])): ?>
            <p>Current image:</p>
            <img
                class="current-image"
                src="../images/<?= htmlspecialchars(basename($item['image'])) ?>"
                alt="Current menu item"
            >
        <?php endif; ?>

        <button type="submit">Save Changes</button>
        <a class="cancel" href="items.php">Cancel</a>
    </form>
</div>
</body>
</html>
```