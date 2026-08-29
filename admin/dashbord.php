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
   COUNT PRODUCTS
   ========================================== */

$productResult = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
);

if (!$productResult) {
    die("Unable to count products: " . $conn->error);
}

$productCount = $productResult->fetch_assoc()['total'];


/* ==========================================
   COUNT ORDERS
   ========================================== */

$orderResult = $conn->query(
    "SELECT COUNT(*) AS total FROM orders"
);

if (!$orderResult) {
    die("Unable to count orders: " . $conn->error);
}

$orderCount = $orderResult->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body>


<header class="admin-nav">

    <h2>CampusKart Admin</h2>


    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>


        <!-- Correct file name -->
        <a href="product.php">
            Products
        </a>


        <!-- Correct file name -->
        <a href="add_product.php">
            Add Product
        </a>


        <a href="../index.php">
            Website
        </a>


        <a href="login.php?logout=1">
            Logout
        </a>

    </nav>

</header>


<section class="dashboard">

    <h1>Dashboard</h1>


    <div class="stats">


        <div class="stat-card">

            <h2>
                <?= htmlspecialchars($productCount) ?>
            </h2>

            <p>Total Products</p>

        </div>


        <div class="stat-card">

            <h2>
                <?= htmlspecialchars($orderCount) ?>
            </h2>

            <p>Total Orders</p>

        </div>


    </div>

</section>


</body>

</html>