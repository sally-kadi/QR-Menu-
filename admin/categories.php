
<?php
include "db.php";

$result = $conn->query("SELECT * FROM categories ORDER BY id ASC");

if (!$result) {
    die("Database error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Categories - Brew Co.</title>
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    padding: 40px 20px;
    font-family: Arial, sans-serif;
    background: #f5f1eb;
    color: #333;
}
.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 30px;
    background: white;
    border-radius: 14px;
    box-shadow: 0 5px 20px rgba(0,0,0,.08);
    overflow-x: auto;
}
h1 { color: #6f4e37; margin-top: 0; }
.add-button {
    display: inline-block;
    margin-bottom: 20px;
    padding: 11px 16px;
    background: #8b6b91;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
table { width: 100%; border-collapse: collapse; }
th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}
th { background: #8b6b91; color: white; }
tr:nth-child(even) { background: #f8f5f1; }
a { text-decoration: none; }
.edit { color: #287c8e; font-weight: bold; }
.delete { color: #c7475a; font-weight: bold; }
.button {
    display: inline-block;
    padding: 7px 10px;
    margin: 2px;
    color: white;
    border-radius: 5px;
    font-size: 13px;
}
.toggle { background: #c58b35; }
.activate { background: #56806a; }
.active { color: green; font-weight: bold; }
.inactive { color: red; font-weight: bold; }
.back { display: inline-block; margin-top: 20px; color: #6f4e37; }
@media (max-width: 600px) {
    body { padding: 15px 8px; }
    .container { padding: 15px; }
    th, td { padding: 8px; }
}
</style>
</head>
<body>
<div class="container">
<h1>Categories</h1>

<a class="add-button" href="add-category.php">+ Add Category</a>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()) {
    $isActive = (int)$row["status"] === 1;
?>
<tr>
    <td><?= (int)$row["id"] ?></td>
    <td><?= htmlspecialchars($row["name"]) ?></td>
    <td>
        <?php if ($isActive) { ?>
            <span class="active">Active</span>
        <?php } else { ?>
            <span class="inactive">Inactive</span>
        <?php } ?>
    </td>
    <td>
        <a class="button <?= $isActive ? 'toggle' : 'activate' ?>"
           href="toggle-category.php?id=<?= (int)$row['id'] ?>"
           onclick="return confirm('Change this category status?')">
           <?= $isActive ? 'Deactivate' : 'Activate' ?>
        </a>

        <a class="edit"
           href="edit-category.php?id=<?= (int)$row['id'] ?>">Edit</a>
        |
        <a class="delete"
           href="delete-category.php?id=<?= (int)$row['id'] ?>"
           onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
    </td>
</tr>
<?php } ?>
</tbody>
</table>

<a class="back" href="index.php">Back to Dashboard</a>
</div>
</body>
</html>
