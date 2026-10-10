
<?php
require_once __DIR__ . '/db.php';

$message = '';

$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int) $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/';
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid('item_', true) . '.' . $extension;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                $image = 'images/' . $fileName;

                $stmt = $conn->prepare(
                    "INSERT INTO menu_items
                    (category_id, name, description, price, image)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "issds",
                    $category_id,
                    $name,
                    $description,
                    $price,
                    $image
                );

                if ($stmt->execute()) {
                    header("Location: items.php");
                    exit;
                }

                $message = "Database error: " . $stmt->error;
                $stmt->close();
            } else {
                $message = "Image upload failed.";
            }
        } else {
            $message = "Please use a JPG, PNG, or WEBP image.";
        }
    } else {
        $message = "Please select an image.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - Brew Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f1eb;
            font-family: Arial, sans-serif;
            color: #38291f;
        }
        .form-card {
            max-width: 750px;
            margin: 45px auto;
            padding: 30px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 20px #00000012;
        }
        .btn-coffee {
            background: #5b3928;
            color: white;
        }
        .btn-coffee:hover {
            background: #3e261b;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-card">
        <h2 class="mb-4">Add Menu Item</h2>

        <?php if ($message !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= (int)$cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Price ($)</label>
                <input type="number" name="price" step="0.01" min="0"
                       class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Item Image</label>
                <input type="file" name="image" class="form-control"
                       accept=".jpg,.jpeg,.png,.webp" required>
            </div>

            <button type="submit" class="btn btn-coffee">Add Item</button>
            <a href="items.php" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
