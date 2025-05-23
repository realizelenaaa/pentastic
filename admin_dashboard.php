<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('config/db.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Pentastic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px 40px;
            color: #333;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h2 {
            color: #222;
            margin: 0;
        }

        a.logout {
            background-color: #ff4d4f;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        a.logout:hover {
            background-color: #d9363e;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .top-bar h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .add-buttons a {
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-left: 10px;
            transition: background-color 0.3s ease;
        }

        .add-buttons a:nth-child(2) {
            background-color: #28a745;
        }

        .add-buttons a:hover {
            filter: brightness(90%);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        th,
        td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background-color: #f7f7f7;
            color: #444;
            font-weight: 600;
        }

        td img {
            width: 60px;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        td img:hover {
            transform: scale(2.5);
            z-index: 100;
            position: relative;
        }

        .status-available {
            color: #28a745;
            font-weight: 600;
        }

        .status-out {
            color: #dc3545;
            font-weight: 600;
        }

        .action-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            margin: 0 6px;
            transition: color 0.3s ease;
        }

        .action-links a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        h3.section-title {
            color: #555;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .add-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }

            td,
            th {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h2>Welcome Admin, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <a href="logout.php" class="logout" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
    </header>


    <div class="top-bar">
        <h3>Product Management</h3>
        <div class="add-buttons">
            <a href="add_product.php">+ Add New Pen</a>
            <a href="add_product_ink.php">+ Add New Ink</a>
        </div>
    </div>

    <!-- Pens Table -->
    <h3 class="section-title">Pens</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php
        $pens = $conn->query("SELECT * FROM pens");
        while ($row = $pens->fetch_assoc()) {
            $status_class = $row['quantity'] <= 0 ? "status-out" : "status-available";
            $status = $row['quantity'] <= 0 ? "Out of Stock" : "Available";
            $imagePath = htmlspecialchars($row['image']);
            $fullPath = __DIR__ . '/' . $row['image'];
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td class="<?= $status_class ?>"><?= $status ?></td>
                <td>
                    <?php if (!empty($row['image']) && file_exists($fullPath)) : ?>
                        <img src="<?= $imagePath ?>" alt="Pen Image">
                    <?php else : ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td class="action-links">
                    <a href="edit_product.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this product?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Inks Table -->
    <h3 class="section-title">Inks</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Brand</th>
            <th>Color</th>
            <th>Volume</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php
        $inks = $conn->query("SELECT * FROM inks");
        while ($row = $inks->fetch_assoc()) {
            $status_class = $row['quantity'] <= 0 ? "status-out" : "status-available";
            $status = $row['quantity'] <= 0 ? "Out of Stock" : "Available";
            $imagePath = htmlspecialchars($row['image']);
            $fullPath = __DIR__ . '/' . $row['image'];
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['brand']) ?></td>
                <td><?= htmlspecialchars($row['color']) ?></td>
                <td><?= $row['volume_ml'] ?> ml</td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td class="<?= $status_class ?>"><?= $status ?></td>
                <td>
                    <?php if (!empty($row['image']) && file_exists($fullPath)) : ?>
                        <img src="<?= $imagePath ?>" alt="Ink Image">
                    <?php else : ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td class="action-links">
                    <a href="edit_ink.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="delete_ink_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this ink?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>