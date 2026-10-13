
<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: menu-items.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: menu-items.php");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE menu_items
     SET status = CASE
         WHEN status = 1 THEN 0
         ELSE 1
     END
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: menu-items.php");
exit;
?>
