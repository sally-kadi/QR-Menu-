
<?php
include "db.php";

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = (int) $_GET["id"];

    $stmt = $conn->prepare(
        "UPDATE categories
         SET status = IF(status = 1, 0, 1)
         WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: categories.php");
exit;
