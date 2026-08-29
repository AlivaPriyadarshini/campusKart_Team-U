<?php

require_once "../config/database.php";


/* ==========================================
   CHECK ADMIN LOGIN
   ========================================== */

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


/* ==========================================
   CHECK PRODUCT ID
   ========================================== */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: product.php");
    exit;
}

$id = intval($_GET['id']);


/* ==========================================
   DELETE PRODUCT
   ========================================== */

$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ?"
);

if (!$stmt) {
    die("Database error: " . $conn->error);
}


$stmt->bind_param("i", $id);


if (!$stmt->execute()) {
    die("Unable to delete product: " . $stmt->error);
}


$stmt->close();


/* ==========================================
   REDIRECT
   ========================================== */

header("Location: product.php");
exit;

?>