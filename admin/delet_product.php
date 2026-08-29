<?php

require_once "../config/database.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

header("Location: products.php");

exit;
?>