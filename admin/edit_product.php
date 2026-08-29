<?php

require_once "../config/database.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);
    $category = trim($_POST['category']);
    $stock = intval($_POST['stock']);

    $stmt = $conn->prepare("
        UPDATE products
        SET name = ?,
            description = ?,
            price = ?,
            image = ?,
            category = ?,
            stock = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssdssii",
        $name,
        $description,
        $price,
        $image,
        $category,
        $stock,
        $id
    );

    $stmt->execute();

    header("Location: products.php");
    exit;
}

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Product</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<section class="admin-form">

    <h1>Edit Product</h1>

    <form method="POST">

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product['name']) ?>"
            required
        >

        <textarea
            name="description"
            required
        ><?= htmlspecialchars($product['description']) ?></textarea>

        <input
            type="number"
            name="price"
            value="<?= $product['price'] ?>"
            step="0.01"
            required
        >

        <input
            type="text"
            name="image"
            value="<?= htmlspecialchars($product['image']) ?>"
        >

        <input
            type="text"
            name="category"
            value="<?= htmlspecialchars($product['category']) ?>"
            required
        >

        <input
            type="number"
            name="stock"
            value="<?= $product['stock'] ?>"
            required
        >

        <button class="btn" type="submit">
            Update Product
        </button>

    </form>

</section>

</body>
</html>