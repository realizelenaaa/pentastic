<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('config/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = trim($_POST['brand']);
    $color = trim($_POST['color']);
    $volume_ml = intval($_POST['volume_ml']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            $uploadDir = 'uploads/ink_images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, PNG, and GIF images are allowed.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO inks (brand, color, volume_ml, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiis", $brand, $color, $volume_ml, $price, $quantity, $imagePath);
        if ($stmt->execute()) {
            $success = "Ink product added successfully!";
        } else {
            $error = "Error adding ink product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add New Ink - Pentastic Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9fb;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 540px;
            padding: 20px;
        }

        h2 {
            color: #222;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 15px;
            font-weight: 600;
            color: #555;
        }

        input[type=text],
        input[type=number],
        textarea,
        input[type=file] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            resize: vertical;
        }

        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            font-weight: 600;
            text-align: center;
        }

        .error {
            color: #dc3545;
        }

        .success {
            color: #28a745;
        }

        a.back {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }

        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Add New Ink Product</h2>

        <?php if ($error): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p class="message success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="image">Image</label>
            <input type="file" name="image" id="image" accept="image/*">

            <label for="brand">Ink Brand</label>
            <input type="text" name="brand" id="brand" required>

            <label for="color">Ink Color</label>
            <input type="text" name="color" id="color" required>

            <label for="volume_ml">Volume (ml)</label>
            <input type="number" name="volume_ml" id="volume_ml" min="1" required>

            <label for="price">Price (₱)</label>
            <input type="number" name="price" id="price" min="0" step="0.01" required>

            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" min="0" required>

            <button type="submit">Add Ink Product</button>
        </form>

        <a href="admin_dashboard.php" class="back">← Back to Admin Dashboard</a>
    </div>

</body>

</html>