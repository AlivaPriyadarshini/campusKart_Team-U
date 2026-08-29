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
   HANDLE FORM SUBMISSION
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
       INSERT PRODUCT
       ========================================== */

    $stmt = $conn->prepare("
        INSERT INTO products
        (name, description, price, image, category, stock)
        VALUES (?, ?, ?, ?, ?, ?)
    ");


    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


    $stmt->bind_param(
        "ssdssi",
        $name,
        $description,
        $price,
        $image,
        $category,
        $stock
    );


    if (!$stmt->execute()) {
        die("Product could not be added: " . $stmt->error);
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

    <title>Add Product</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

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
            min="0"
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
            min="0"
            required
        >


        <button
            class="btn"
            type="submit"
        >
            Add Product
        </button>


        <a href="product.php">
            Cancel
        </a>


    </form>

</section>


</body>

</html>