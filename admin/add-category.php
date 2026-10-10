<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");

    if ($name === "") {
        $message = "Please enter a category name.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO categories (name) VALUES (?)"
        );

        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: categories.php");
            exit;
        } else {
            $message = "Error adding category: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category - Brew Co.</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f1eb;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #6f4e37;
        }

        label {
            display: block;
            margin: 15px 0 8px;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button, .cancel {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Add Category</h1>

        <?php if ($message !== ""): ?>
            <p class="message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Category Name</label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter category name"
                required
            >

            <button type="submit">Add Category</button>

            <a class="cancel" href="categories.php">Cancel</a>
        </form>
    </div>
</body>
</html>