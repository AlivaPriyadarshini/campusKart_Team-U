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
   GET PRODUCT
   ========================================== */

$stmt = $conn->prepare(
    "SELECT * FROM products WHERE id = ?"
);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Unable to fetch product: " . $stmt->error);
}

$product = $stmt->get_result()->fetch_assoc();

$stmt->close();


/* ==========================================
   CHECK PRODUCT EXISTS
   ========================================== */

if (!$product) {
    die("Product not found.");
}


/* ==========================================
   UPDATE PRODUCT
   ========================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);


    /* ==========================================
       VALIDATION
       ========================================== */

    if ($name === '') {
        die("Product name is required.");
    }

    if ($description === '') {
        die("Product description is required.");
    }

    if ($category === '') {
        die("Category is required.");
    }

    if ($price < 0) {
        die("Price cannot be negative.");
    }

    if ($stock < 0) {
        die("Stock cannot be negative.");
    }


    /* ==========================================
       UPDATE DATABASE
       ========================================== */

    $stmt = $conn->prepare("
        UPDATE products
        SET
            name = ?,
            description = ?,
            price = ?,
            image = ?,
            category = ?,
            stock = ?
        WHERE id = ?
    ");


    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


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


    if (!$stmt->execute()) {
        die("Product could not be updated: " . $stmt->error);
    }


    $stmt->close();


    /* ==========================================
       REDIRECT
       ========================================== */

    header("Location: product.php");
    exit;
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Product</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body>


<section class="admin-form">

    <h1>Edit Product</h1>


    <form method="POST">


        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product['name']) ?>"
            placeholder="Product Name"
            required
        >


        <textarea
            name="description"
            placeholder="Product Description"
            required
        ><?= htmlspecialchars($product['description']) ?></textarea>


        <input
            type="number"
            name="price"
            value="<?= htmlspecialchars($product['price']) ?>"
            placeholder="Price"
            step="0.01"
            min="0"
            required
        >


        <input
            type="text"
            name="image"
            value="<?= htmlspecialchars($product['image']) ?>"
            placeholder="Image URL"
        >


        <input
            type="text"
            name="category"
            value="<?= htmlspecialchars($product['category']) ?>"
            placeholder="Category"
            required
        >


        <input
            type="number"
            name="stock"
            value="<?= htmlspecialchars($product['stock']) ?>"
            placeholder="Stock"
            min="0"
            required
        >


        <button
            class="btn"
            type="submit"
        >
            Update Product
        </button>


    </form>

</section>


</body>

</html>