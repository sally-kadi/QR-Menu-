```php
<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET" ||
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])) {
    header("Location: items.php");
    exit();
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: items.php");
    exit();
} else {
    echo "Error deleting item: " . htmlspecialchars($stmt->error);
    $stmt->close();
}
?>
```