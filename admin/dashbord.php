<?php

require_once "../config/database.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$productCount =
    $conn->query("SELECT COUNT(*) AS total FROM products")
         ->fetch_assoc()['total'];

$orderCount =
    $conn->query("SELECT COUNT(*) AS total FROM orders")
         ->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header class="admin-nav">

    <h2>CampusKart Admin</h2>

    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="products.php">
            Products
        </a>

        <a href="add-product.php">
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
                <?= $productCount ?>
            </h2>

            <p>Total Products</p>

        </div>

        <div class="stat-card">

            <h2>
                <?= $orderCount ?>
            </h2>

            <p>Total Orders</p>

        </div>

    </div>

</section>

</body>
</html>