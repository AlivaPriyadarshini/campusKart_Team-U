<?php
require_once "config/database.php";

$result = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CampusKart - Student Marketplace</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="navbar">

        <div class="logo">
            Campus<span>Kart</span>
        </div>

        <nav>
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="cart.php">Cart 🛒</a>
            <a href="admin/login.php">Admin</a>
        </nav>

    </header>


    <section class="hero">

        <div class="hero-content">

            <h1>Everything You Need On Campus</h1>

            <p>
                Shop stationery, electronics, fashion and college essentials
                at CampusKart.
            </p>

            <a href="products.php" class="btn">
                Shop Now
            </a>

        </div>

    </section>


    <section class="section">

        <h2>Popular Products</h2>

        <div class="product-grid">

            <?php while ($product = $result->fetch_assoc()): ?>

                <div class="product-card">

                    <img
                        src="<?= htmlspecialchars($product['image']) ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>">

                    <div class="product-info">

                        <span class="category">
                            <?= htmlspecialchars($product['category']) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>

                        <p class="price">
                            ₹<?= number_format($product['price'], 2) ?>
                        </p>

                        <a
                            href="products.php?id=<?= $product['id'] ?>"
                            class="btn">
                            View Product
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </section>


    <footer>
        <p>© 2026 CampusKart | Student E-Commerce Platform</p>
    </footer>

    <script src="js/script.js"></script>

</body>

</html>