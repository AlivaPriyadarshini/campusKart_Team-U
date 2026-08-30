<?php
require_once "../config/database.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: product.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: product.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Unable to delete product: " . $stmt->error);
}
$stmt->close();

$image = trim($product['image'] ?? "");
$productsFolder = realpath(__DIR__ . "/../image/products");
if ($productsFolder && $image !== "") {
    $oldPath = realpath(__DIR__ . "/../" . ltrim($image, "/\\"));
    if ($oldPath && strpos($oldPath, $productsFolder) === 0 && is_file($oldPath)) {
        unlink($oldPath);
    }
}

header("Location: product.php");
exit;
?>
