<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    $imagePath = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imagePath = $targetDir . time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $imageName);

        if (!move_uploaded_file($imageTmp, $imagePath)) {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Image is required.";
    }

    if (!isset($error)) {
        if ($name === '' || $price <= 0 || $quantity < 0) {
            $error = "Please fill all fields correctly.";
        } else {
            $stmt = $conn->prepare("INSERT INTO pens (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $name, $description, $price, $quantity, $imagePath);
            if ($stmt->execute()) {
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "Failed to add product to the database.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add New Pen Product - Pentastic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            margin: 40px auto;
            max-width: 480px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #222;
        }

        form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 14px;
            background-color: #007bff;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #dc3545;
            font-weight: 600;
            text-align: center;
        }

        a.back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            font-weight: 600;
            text-decoration: none;
        }

        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <h2>Add New Pen Product</h2>

    <?php if (isset($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4"></textarea>

        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <label for="price">Price (₱):</label>
        <input type="number" id="price" name="price" step="0.01" min="0.01" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="0" required>

        <button type="submit">Add Pen Product</button>
    </form>

    <a href="admin_dashboard.php" class="back-link">← Back to Admin Dashboard</a>

</body>

</html>