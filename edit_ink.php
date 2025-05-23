<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('config/db.php');

if (!isset($_GET['id'])) {
    die("Ink ID is required.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM inks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ink = $result->fetch_assoc();

if (!$ink) {
    die("Ink not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = $_POST['brand'];
    $color = $_POST['color'];
    $volume_ml = $_POST['volume_ml'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Handle image upload
    $image = $ink['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $image = $imagePath;
    }

    $update = $conn->prepare("UPDATE inks SET brand=?, color=?, volume_ml=?, price=?, quantity=?, image=? WHERE id=?");
    $update->bind_param("ssiddsi", $brand, $color, $volume_ml, $price, $quantity, $image, $id);
    $update->execute();

    header("Location: admin_dashboard.php?msg=Ink updated successfully!");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Ink</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9fb;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #444;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
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
    <div class="container">
        <h2>Edit Ink</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Brand:</label>
            <input type="text" name="brand" value="<?= htmlspecialchars($ink['brand']) ?>" required>

            <label>Color:</label>
            <input type="text" name="color" value="<?= htmlspecialchars($ink['color']) ?>" required>

            <label>Volume (mL):</label>
            <input type="number" name="volume_ml" value="<?= $ink['volume_ml'] ?>" step="0.1" required>

            <label>Price (₱):</label>
            <input type="number" name="price" value="<?= $ink['price'] ?>" step="0.01" required>

            <label>Quantity:</label>
            <input type="number" name="quantity" value="<?= $ink['quantity'] ?>" required>

            <label>Image:</label>
            <input type="file" name="image" accept="image/*">

            <?php if (!empty($ink['image']) && file_exists($ink['image'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?= htmlspecialchars($ink['image']) ?>" alt="Ink Image">
                </div>
            <?php endif; ?>

            <button type="submit">Update Ink</button>
        </form>
        <a class="cancel-link" href="admin_dashboard.php">Cancel</a>
    </div>
</body>

</html>