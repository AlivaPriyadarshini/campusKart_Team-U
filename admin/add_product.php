<?php

require_once "../config/database.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);
    $category = trim($_POST['category']);
    $stock = intval($_POST['stock']);

    $stmt = $conn->prepare("
        INSERT INTO products
        (name, description, price, image, category, stock)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdssi",
        $name,
        $description,
        $price,
        $image,
        $category,
        $stock
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

    <title>Add Product</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<section class="admin-form">

    <h1>Add New Product</h1>

    <form method="POST">

        <input
            type="text"
            name="name"
            placeholder="Product Name"
            required
        >

        <textarea
            name="description"
            placeholder="Product Description"
            required
        ></textarea>

        <input
            type="number"
            name="price"
            placeholder="Price"
            step="0.01"
            required
        >

        <input
            type="text"
            name="image"
            placeholder="Image URL"
        >

        <input
            type="text"
            name="category"
            placeholder="Category"
            required
        >

        <input
            type="number"
            name="stock"
            placeholder="Stock"
            required
        >

        <button class="btn" type="submit">
            Add Product
        </button>

        <a href="products.php">
            Cancel
        </a>

    </form>

</section>

</body>
</html>