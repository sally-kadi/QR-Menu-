<?php

include "db.php";

$id = $_GET["id"];

$result = $conn->query("SELECT * FROM categories WHERE id = $id");
$category = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];

    $sql = "UPDATE categories SET name = '$name' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: categories.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
</head>
<body>

<h1>Edit Category</h1>

<form method="POST">

    <label>Category Name:</label>
    <input
        type="text"
        name="name"
        value="<?php echo $category['name']; ?>"
        required
    >

    <button type="submit">Update Category</button>

</form>

</body>
</html>