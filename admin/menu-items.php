
<?php
include "db.php";

$sql = "SELECT
            menu_items.id,
            menu_items.name,
            menu_items.description,
            menu_items.price,
            menu_items.image,
            menu_items.status,
            categories.name AS category_name
        FROM menu_items
        LEFT JOIN categories
            ON menu_items.category_id = categories.id
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
        * {
            box-sizing: border-box;
        }

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
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #00000015;
            overflow-x: auto;
        }

        h1 {
            color: #5c3825;
            margin-top: 0;
        }

        .add-btn {
            display: inline-block;
            background: #6f4e37;
            color: white;
            padding: 11px 16px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .add-btn:hover {
            background: #4e3525;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #6f4e37;
            color: white;
        }

        tr:nth-child(even) {
            background: #faf7f3;
        }

        .product-img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 7px;
        }

        .action-link {
            color: #6f4e37;
            margin-right: 8px;
            text-decoration: none;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .delete-link {
            color: #c0392b;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #287a46;
            cursor: pointer;
            font-size: 14px;
            margin-right: 8px;
            padding: 0;
        }

        .toggle-btn:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6f4e37;
            text-decoration: none;
        }

        .status-active {
            color: #287a46;
            font-weight: bold;
        }

        .status-inactive {
            color: #c0392b;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Menu Items</h1>

    <a class="add-btn" href="add-menu-item.php">
        + Add Menu Item
    </a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?= (int)$item["id"] ?>
                    </td>

                    <td>
                        <?php
                        $imagePath = trim($item["image"] ?? "");

                        $imageFile = dirname(__DIR__)
                            . DIRECTORY_SEPARATOR
                            . str_replace(
                                "/",
                                DIRECTORY_SEPARATOR,
                                $imagePath
                            );
                        ?>

                        <?php if (
                            $imagePath !== "" &&
                            is_file($imageFile)
                        ): ?>
                            <img
                                class="product-img"
                                src="../<?= htmlspecialchars($imagePath) ?>"
                                alt="<?= htmlspecialchars($item["name"]) ?>"
                            >
                        <?php else: ?>
                            <span>No image</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($item["name"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($item["description"] ?? "") ?>
                    </td>

                    <td>
                        $<?= number_format((float)$item["price"], 2) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $item["category_name"] ?? "No category"
                        ) ?>
                    </td>

                    <td>
                        <?php if (!empty($item["status"])): ?>
                            <span class="status-active">Active</span>
                        <?php else: ?>
                            <span class="status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <form
                            action="toggle-menu-item.php"
                            method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Change this item status?');"
                        >
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int)$item["id"] ?>"
                            >

                            <button class="toggle-btn" type="submit">
                                <?= !empty($item["status"])
                                    ? "Deactivate"
                                    : "Activate" ?>
                            </button>
                        </form>

                        <a
                            class="action-link"
                            href="edit-menu-item.php?id=<?= (int)$item["id"] ?>"
                        >
                            Edit
                        </a>

                        <a
                            class="action-link delete-link"
                            href="delete-menu-item.php?id=<?= (int)$item["id"] ?>"
                            onclick="return confirm('Are you sure you want to delete this item?');"
                        >
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <tr>
                <td colspan="8">No menu items found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a class="back-link" href="index.php">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>
