<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

include('config/db.php');

// Check if product ID is passed
if (!isset($_GET['id'])) {
    echo "<p class='error-message'>Product ID is missing.</p>";
    exit;
}

$product_id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $imagePath = '';

    // Fetch current image
    $stmt = $conn->prepare("SELECT image FROM pens WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingProduct = $result->fetch_assoc();

    if (!$existingProduct) {
        $error = "Product not found.";
    } else {
        $imagePath = $existingProduct['image'];

        // Check if a new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $targetDir = "uploads/";

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $newImagePath = $targetDir . time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $imageName);

            if (move_uploaded_file($imageTmp, $newImagePath)) {
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete old image
                }
                $imagePath = $newImagePath;
            } else {
                $error = "Failed to upload new image.";
            }
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("UPDATE pens SET name=?, description=?, price=?, quantity=?, image=? WHERE id=?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $imagePath, $product_id);
            if ($stmt->execute()) {
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "Failed to update product.";
            }
        }
    }
}

// Fetch product details for editing form
$stmt = $conn->prepare("SELECT * FROM pens WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p class='error-message'>Product not found.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Pen Product - Pentastic Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .edit-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 520px;
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        img {
            max-width: 100px;
            margin-bottom: 12px;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            text-decoration: none;
            color: #777;
        }

        .cancel-link:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .current-image {
            text-align: center;
            margin-bottom: 18px;
        }

        .current-image img {
            width: 80px;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="edit-container">
        <h2>Edit Pen Product: <?= htmlspecialchars($product['name']) ?></h2>

        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>

            <input type="file" name="image" accept="image/*">

            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            <input type="number" name="quantity" min="0" value="<?= htmlspecialchars($product['quantity']) ?>" required>
            <label>Product Image:</label>
            <div class="current-image">
                <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Current Product Image">
                <?php else: ?>
                    <p>No Image</p>
                <?php endif; ?>
                <button type="submit">Update Product</button>
        </form>
        <a class="cancel-link" href="admin_dashboard.php">Cancel</a>
    </div>
</body>

</html>