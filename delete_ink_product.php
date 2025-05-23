<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
include('config/db.php');

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM inks WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

header("Location: admin_dashboard.php");
exit;
