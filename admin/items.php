```php
<?php
include "db.php";

$sql = "SELECT menu_items.*, categories.name AS category_name
        FROM menu_items
        LEFT JOIN categories ON menu_items.category_id = categories.id
        ORDER BY menu_items.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items - Brew Co.</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f1eb;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,.1);
            overflow-x: auto;
        }

        h1 { color: #6f4e37; }

        .add-button {
            display: inline-block;
            background: #8b6b61;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #8b6b61;
            color: white;
        }

        tr:nth-child(even) { background: #f9f6f2; }

        .item-image {
            width: 80px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
        }

        .button {
            display: inline-block;
            padding: 7px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            margin: 2px;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .edit-button { background: #56806a; }
        .delete-button { background: #c75050; }
        .toggle-button { background: #c58b35; }
        .activate-button { background: #56806a; }

        .active { color: green; font-weight: bold; }
        .inactive { color: red; font-weight: bold; }
        .empty { text-align: center; padding: 20px; }
    </style>
</head>

<body>
<div class="container">

    <h1>Menu Items</h1>

    <a class="add-button" href="add-item.php">+ Add Item</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price ($)</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($item = $result->fetch_assoc()): ?>
                <?php $isActive = (int)($item['status'] ?? 0) === 1; ?>

                <tr>
                    <td><?= (int)$item['id'] ?></td>

                    <td>
                        <?= htmlspecialchars($item['category_name'] ?? 'No Category') ?>
                    </td>

                    <td><?= htmlspecialchars($item['name'] ?? '') ?></td>

                    <td><?= htmlspecialchars($item['description'] ?? '') ?></td>

                    <td><?= htmlspecialchars($item['price'] ?? '') ?></td>

                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img
                                class="item-image"
                                src="../images/<?= htmlspecialchars(basename($item['image'])) ?>"
                                alt="<?= htmlspecialchars($item['name'] ?? 'Menu item') ?>"
                            >
                        <?php else: ?>
                            No image
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($isActive): ?>
                            <span class="active">Active</span>
                        <?php else: ?>
                            <span class="inactive">Inactive</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a
                            class="button <?= $isActive ? 'toggle-button' : 'activate-button' ?>"
                            href="toggle-item.php?id=<?= (int)$item['id'] ?>"
                            onclick="return confirm('Change this item status?')"
                        >
                            <?= $isActive ? 'Deactivate' : 'Activate' ?>
                        </a>

                        <a
                            class="button edit-button"
                            href="edit-item.php?id=<?= (int)$item['id'] ?>"
                        >Edit</a>

                        <a
                            class="button delete-button"
                            href="delete-item.php?id=<?= (int)$item['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this item?')"
                        >Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <tr>
                <td colspan="8" class="empty">
                    No menu items found. Click Add Item to create one.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php">Back to Dashboard</a>

</div>
</body>
</html>
```