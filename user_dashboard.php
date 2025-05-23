<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit;
}

include('config/db.php');

$purchase_message = "";

// Handle pen or ink purchase
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $type = $_POST['type']; // 'pen' or 'ink'

    if ($quantity < 1) {
        $purchase_message = "Invalid quantity.";
    } else {
        $table = $type === 'ink' ? 'inks' : 'pens';

        // Fetch current stock and price
        $stmt = $conn->prepare("SELECT quantity, price FROM $table WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->bind_result($available_quantity, $price);

            if ($stmt->fetch()) {
                $stmt->close();

                if ($available_quantity >= $quantity) {
                    // Update stock quantity
                    $new_quantity = $available_quantity - $quantity;
                    $update_stmt = $conn->prepare("UPDATE $table SET quantity = ? WHERE id = ?");
                    if ($update_stmt) {
                        $update_stmt->bind_param("ii", $new_quantity, $product_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }

                    // Insert purchase record
                    $total_price = $price * $quantity;
                    $pen_id = $type === 'pen' ? $product_id : null;
                    $ink_id = $type === 'ink' ? $product_id : null;

                    $insert_stmt = $conn->prepare("INSERT INTO purchases (user_id, pen_id, ink_id, quantity, total_price, purchased_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    if ($insert_stmt) {
                        $insert_stmt->bind_param("iiiid", $user_id, $pen_id, $ink_id, $quantity, $total_price);
                        if ($insert_stmt->execute()) {
                            // Custom success message based on product type
                            $product_name = "";

                            if ($type === 'pen') {
                                $name_stmt = $conn->prepare("SELECT name FROM pens WHERE id = ?");
                                $name_stmt->bind_param("i", $product_id);
                                $name_stmt->execute();
                                $name_stmt->bind_result($product_name);
                                $name_stmt->fetch();
                                $name_stmt->close();

                                $purchase_message = "Successfully purchased the pen: " . htmlspecialchars($product_name);
                            } else {
                                $ink_stmt = $conn->prepare("SELECT brand, color FROM inks WHERE id = ?");
                                $ink_stmt->bind_param("i", $product_id);
                                $ink_stmt->execute();
                                $ink_stmt->bind_result($brand, $color);
                                $ink_stmt->fetch();
                                $ink_stmt->close();

                                $purchase_message = "Successfully purchased the ink: " . htmlspecialchars($brand) . " - " . htmlspecialchars($color);
                            }
                        } else {
                            $purchase_message = "Failed to record purchase: " . $insert_stmt->error;
                        }
                        $insert_stmt->close();
                    } else {
                        $purchase_message = "Failed to prepare insert statement: " . $conn->error;
                    }
                } else {
                    $purchase_message = "Not enough stock available.";
                }
            } else {
                $purchase_message = "Product not found.";
                $stmt->close(); // close even if fetch failed
            }
        } else {
            $purchase_message = "Database error: " . $conn->error;
        }
    }
}

// Determine sorting
$sort_by = 'pens.name';
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'price') $sort_by = 'pens.price';
    elseif ($_GET['sort'] === 'name') $sort_by = 'pens.name';
}

// Query pens with compatible inks
$sql = "
    SELECT pens.*, inks.id AS ink_id, inks.brand, inks.color, inks.price AS ink_price, inks.quantity AS ink_quantity, inks.image AS ink_image
    FROM pens
    LEFT JOIN pen_ink_compatibility ON pens.id = pen_ink_compatibility.pen_id
    LEFT JOIN inks ON pen_ink_compatibility.ink_id = inks.id
    ORDER BY $sort_by ASC
";
$result = $conn->query($sql);

$pens = [];
while ($row = $result->fetch_assoc()) {
    $pen_id = $row['id'];
    if (!isset($pens[$pen_id])) {
        $pens[$pen_id] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'quantity' => $row['quantity'],
            'image' => $row['image'],
            'inks' => []
        ];
    }
    if ($row['ink_id']) {
        $pens[$pen_id]['inks'][] = [
            'id' => $row['ink_id'],
            'brand' => $row['brand'],
            'color' => $row['color'],
            'price' => $row['ink_price'],
            'quantity' => $row['ink_quantity'],
            'image' => $row['ink_image']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Dashboard - Pentastic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9fb;
            margin: 40px;
            color: #333;
        }

        h2 {
            color: #222;
        }

        a.logout {
            background-color: #ff4d4f;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            float: right;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        a.logout:hover {
            background-color: #d9363e;
        }

        h3 {
            margin-top: 60px;
            margin-bottom: 30px;
            font-weight: 600;
            color: #555;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgb(0 0 0 / 0.15);
        }

        .image-wrapper {
            position: relative;
            width: 100%;
            height: 180px;
            background-color: #ffffff;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            border-radius: 10px;
            object-fit: contain;
        }

        .quantity-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .quantity-badge.status-out {
            background-color: #dc3545;
            color: white;
        }

        .product-name {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: #222;
        }

        .product-description {
            flex-grow: 1;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .product-price {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #007bff;
        }

        .status-available {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .status-out {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .buy-form input[type=number] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .buy-form button {
            width: 100%;
            padding: 10px;
            background-color: #17a2b8;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .buy-form button:hover {
            background-color: #138496;
        }


        .not-available {
            text-align: center;
            color: #888;
            font-style: italic;
            font-weight: 600;
            margin-top: 10px;
        }

        .compatible-inks-container {
            margin-top: 20px;
        }

        .compatible-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #444;
            font-size: 1rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .inks-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ink-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f6fa;
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
        }

        .ink-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-width: 180px;
        }

        .ink-info img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 6px;
        }

        .ink-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
        }

        .ink-price {
            font-size: 0.9rem;
            color: #007bff;
            font-weight: 500;
        }

        .ink-form {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 120px;
        }

        .ink-form input[type="number"] {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .ink-form button {
            background-color: #17a2b8;
            padding: 6px;
            font-size: 0.9rem;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .ink-form button:hover {
            background-color: #138496;
        }

        .message {
            font-weight: 600;
            margin: 20px 0;
            padding: 12px;
            border-radius: 6px;
        }

        .message.success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <a href="logout.php" class="logout" onclick="return confirmLogout()">Logout</a>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

    <?php if ($purchase_message): ?>
        <p class="message <?= strpos($purchase_message, 'Successfully') === 0 ? 'success' : 'error' ?>">
            <?= htmlspecialchars($purchase_message) ?>
        </p>
    <?php endif; ?>

    <form method="GET" aria-label="Sort pens">
        <label for="sort">Sort pens by:</label>
        <select id="sort" name="sort" onchange="this.form.submit()">
            <option value="name" <?= $sort_by == 'pens.name' ? 'selected' : '' ?>>Name</option>
            <option value="price" <?= $sort_by == 'pens.price' ? 'selected' : '' ?>>Price</option>
        </select>
    </form>

    <h3>Pens & Inks</h3>

    <div class="products-grid">
        <?php foreach ($pens as $pen): ?>
            <div class="product-card" role="region" aria-label="Pen: <?= htmlspecialchars($pen['name']) ?>">
                <div class="image-wrapper">
                    <?php if (!empty($pen['image']) && file_exists($pen['image'])): ?>
                        <img src="<?= htmlspecialchars($pen['image']) ?>" alt="Image of <?= htmlspecialchars($pen['name']) ?>" class="product-image" />
                    <?php else: ?>
                        <div class="product-image" style="font-size: 14px; color: #aaa;">No Image</div>
                    <?php endif; ?>
                    <div class="quantity-badge <?= $pen['quantity'] == 0 ? 'status-out' : '' ?>" aria-label="Available quantity"><?= $pen['quantity'] ?></div>
                </div>
                <div class="product-name"><?= htmlspecialchars($pen['name']) ?></div>
                <div class="product-description"><?= nl2br(htmlspecialchars($pen['description'])) ?></div>
                <div class="product-price">₱<?= number_format($pen['price'], 2) ?></div>
                <div class="<?= $pen['quantity'] > 0 ? 'status-available' : 'status-out' ?>">
                    <?= $pen['quantity'] > 0 ? 'In stock' : 'Out of stock' ?>
                </div>
                <form class="buy-form" method="POST" aria-label="Buy pen <?= htmlspecialchars($pen['name']) ?>" onsubmit="return confirmPurchase('pen', '<?= htmlspecialchars($pen['name']) ?>')">
                    <input type="hidden" name="product_id" value="<?= $pen['id'] ?>" />
                    <input type="hidden" name="type" value="pen" />
                    <label for="quantity_pen_<?= $pen['id'] ?>" class="sr-only">Quantity</label>
                    <input id="quantity_pen_<?= $pen['id'] ?>" type="number" name="quantity" min="1" max="<?= $pen['quantity'] ?>" value="1" required <?= $pen['quantity'] == 0 ? 'disabled' : '' ?> />
                    <button type="submit" <?= $pen['quantity'] == 0 ? 'disabled' : '' ?>>Buy Pen</button>
                </form>

                <?php if (count($pen['inks']) > 0): ?>
                    <div class="compatible-inks-container" aria-label="Compatible inks for <?= htmlspecialchars($pen['name']) ?>">
                        <div class="compatible-title">Compatible Inks:</div>
                        <div class="inks-grid">
                            <?php foreach ($pen['inks'] as $ink): ?>
                                <div class="ink-card" role="group" aria-label="Ink brand <?= htmlspecialchars($ink['brand']) ?> color <?= htmlspecialchars($ink['color']) ?>">
                                    <div class="ink-info" style="position: relative;">
                                        <?php if (!empty($ink['image']) && file_exists($ink['image'])): ?>
                                            <div style="position: relative; display: inline-block;">
                                                <img src="<?= htmlspecialchars($ink['image']) ?>" alt="Ink <?= htmlspecialchars($ink['brand']) ?> <?= htmlspecialchars($ink['color']) ?>" />
                                                <div class="quantity-badge <?= $ink['quantity'] == 0 ? 'status-out' : '' ?>" style="position: absolute; top: 5px; right: 5px;">
                                                    <?= $ink['quantity'] ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div style="position: relative; width:80px; height:80px; display:flex; align-items:center; justify-content:center; background:#ddd; border-radius:10px; font-size:12px; color:#888;">
                                                No Image
                                                <div class="quantity-badge <?= $ink['quantity'] == 0 ? 'status-out' : '' ?>" style="position: absolute; top: 5px; right: 5px;">
                                                    <?= $ink['quantity'] ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="ink-name"><?= htmlspecialchars($ink['brand']) ?> - <?= htmlspecialchars($ink['color']) ?></div>
                                        <div class="ink-price">₱<?= number_format($ink['price'], 2) ?></div>

                                        <div class="<?= $ink['quantity'] > 0 ? 'status-available' : 'status-out' ?>" style="margin-top: 6px; font-size: 0.9rem;">
                                            <?= $ink['quantity'] > 0 ? 'In stock' : 'Out of stock' ?>
                                        </div>
                                    </div>


                                    <form class="ink-form" method="POST" onsubmit="return confirmPurchase('ink', '<?= htmlspecialchars($ink['brand']) ?> - <?= htmlspecialchars($ink['color']) ?>')">
                                        <input type="hidden" name="product_id" value="<?= $ink['id'] ?>" />
                                        <input type="hidden" name="type" value="ink" />
                                        <label for="quantity_ink_<?= $ink['id'] ?>" class="sr-only">Quantity</label>
                                        <input id="quantity_ink_<?= $ink['id'] ?>" type="number" name="quantity" min="1" max="<?= $ink['quantity'] ?>" value="1" required <?= $ink['quantity'] == 0 ? 'disabled' : '' ?> />
                                        <button type="submit" <?= $ink['quantity'] == 0 ? 'disabled' : '' ?>>Buy Ink</button>
                                    </form>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        function confirmPurchase(type, name) {
            return confirm(`Are you sure you want to buy this ${type === 'pen' ? 'pen' : 'ink'}: "${name}"?`);
        }
    </script>
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>
</body>

</html>